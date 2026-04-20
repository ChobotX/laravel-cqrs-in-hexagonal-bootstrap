<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Command;

use App\Application\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Sso\Contract\Command\LinkSsoIdentityCommand;
use App\Domain\Sso\Contract\Command\LoginViaSsoCommand;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Event\SsoLoginFailed;
use App\Domain\Sso\Contract\Event\SsoLoginSucceeded;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\Repository\UserSsoIdentityRepository;
use App\Domain\Sso\Contract\Service\SsoAuthenticatorRegistry;
use App\Domain\Sso\Contract\Service\SsoLoginSession;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;
use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Domain\User\Contract\Command\MarkUserActivatedCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Repository\UserRepository;
use DateTimeImmutable;
use Throwable;

/** @implements CommandHandler<LoginViaSsoCommand> */
final readonly class LoginViaSsoHandler implements CommandHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
        private UserSsoIdentityRepository $userSsoIdentityRepository,
        private UserRepository $userRepository,
        private SsoAuthenticatorRegistry $ssoAuthenticatorRegistry,
        private CommandBus $commandBus,
        private EventCollector $eventCollector,
        private SsoLoginSession $ssoLoginSession,
    ) {}

    public function handle(Command $command): void
    {
        $configuration = $this->ssoConfigurationRepository->findById(new SsoConfigurationId($command->configurationId));

        if (! $configuration instanceof SsoConfiguration) {
            throw new SsoConfigurationNotFoundException($command->configurationId);
        }

        if (! $configuration->enabled) {
            $this->recordFailure($configuration->id->value, 'configuration_disabled', null, null);

            throw new SsoLoginRejectedException('configuration_disabled');
        }

        try {
            $identity = $this->ssoAuthenticatorRegistry->for($configuration->providerType)
                ->complete($configuration, $command->callbackPayload);
        } catch (Throwable) {
            $this->recordFailure($configuration->id->value, 'authenticator_error', null, null);

            throw new SsoLoginRejectedException('authenticator_error');
        }

        $linked = $this->userSsoIdentityRepository->findBySubject($configuration->id, $identity->subject);

        if ($linked instanceof UserSsoIdentity) {
            $this->recordSuccess($configuration->id->value, $linked->userId->value, $identity, userProvisioned: false);
            $this->ssoLoginSession->setLastResolvedUserId($linked->userId->value);

            return;
        }

        [$user, $provisioned] = $this->provisionUser($configuration, $identity, $command->newUserIdIfProvisioned);

        $this->commandBus->dispatch(new LinkSsoIdentityCommand(
            id: $command->newIdentityId,
            userId: $user->id->value,
            configurationId: $configuration->id->value,
            subject: $identity->subject,
            emailAtLink: $identity->email,
        ));

        $this->recordSuccess(
            $configuration->id->value,
            $user->id->value,
            $identity,
            userProvisioned: $provisioned,
        );
        $this->ssoLoginSession->setLastResolvedUserId($user->id->value);
    }

    /** @return array{0: User, 1: bool} */
    private function provisionUser(SsoConfiguration $ssoConfiguration, SsoIdentity $ssoIdentity, string $newUserId): array
    {
        $existing = $this->userRepository->findByEmail($ssoIdentity->email);

        return match ($ssoConfiguration->jitMode) {
            JitMode::LinkedOnly => $this->requireLinkedUser($ssoConfiguration->id->value, $ssoIdentity),
            JitMode::InvitedOnly => $this->activateInvitedUser($ssoConfiguration->id->value, $ssoIdentity, $existing),
            JitMode::AutoCreate => $this->autoCreateUser($ssoConfiguration, $ssoIdentity, $existing, $newUserId),
        };
    }

    /** @return array{0: User, 1: bool} */
    private function requireLinkedUser(string $configurationId, SsoIdentity $ssoIdentity): array
    {
        $this->recordFailure($configurationId, 'linked_only_no_match', $ssoIdentity->email, $ssoIdentity->subject);

        throw new SsoLoginRejectedException('linked_only_no_match');
    }

    /** @return array{0: User, 1: bool} */
    private function activateInvitedUser(string $configurationId, SsoIdentity $ssoIdentity, ?User $user): array
    {
        if (! $user instanceof User) {
            $this->recordFailure($configurationId, 'invited_only_no_user', $ssoIdentity->email, $ssoIdentity->subject);

            throw new SsoLoginRejectedException('invited_only_no_user');
        }

        if (! $user->isActivated) {
            $this->commandBus->dispatch(new MarkUserActivatedCommand(userId: $user->id->value));
        }

        return [$user, false];
    }

    /** @return array{0: User, 1: bool} */
    private function autoCreateUser(SsoConfiguration $ssoConfiguration, SsoIdentity $ssoIdentity, ?User $user, string $newUserId): array
    {
        if ($user instanceof User) {
            if (! $user->isActivated) {
                $this->commandBus->dispatch(new MarkUserActivatedCommand(userId: $user->id->value));
            }

            return [$user, false];
        }

        if (! $ssoConfiguration->allowedEmailDomains->permits($ssoIdentity->email)) {
            $this->recordFailure($ssoConfiguration->id->value, 'email_domain_not_allowed', $ssoIdentity->email, $ssoIdentity->subject);

            throw new SsoLoginRejectedException('email_domain_not_allowed');
        }

        $this->commandBus->dispatch(new CreateUserCommand(
            id: $newUserId,
            name: $ssoIdentity->name ?? $ssoIdentity->email,
            email: $ssoIdentity->email,
        ));

        $this->commandBus->dispatch(new MarkUserActivatedCommand(userId: $newUserId));

        $created = $this->userRepository->findByEmail($ssoIdentity->email);

        if (! $created instanceof User) {
            $this->recordFailure($ssoConfiguration->id->value, 'user_creation_failed', $ssoIdentity->email, $ssoIdentity->subject);

            throw new SsoLoginRejectedException('user_creation_failed');
        }

        return [$created, true];
    }

    private function recordSuccess(string $configurationId, string $userId, SsoIdentity $ssoIdentity, bool $userProvisioned): void
    {
        $this->eventCollector->collect(new SsoLoginSucceeded(
            configurationId: $configurationId,
            userId: $userId,
            subject: $ssoIdentity->subject,
            email: $ssoIdentity->email,
            userProvisioned: $userProvisioned,
            occurredAt: new DateTimeImmutable,
        ));
    }

    private function recordFailure(string $configurationId, string $reason, ?string $email, ?string $subject): void
    {
        $this->eventCollector->collect(new SsoLoginFailed(
            configurationId: $configurationId,
            reason: $reason,
            email: $email,
            subject: $subject,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
