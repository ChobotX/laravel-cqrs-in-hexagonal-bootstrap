<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Sso\Contract\Command\LinkSsoIdentityCommand;
use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\Event\SsoIdentityLinked;
use App\Domain\Sso\Contract\Repository\UserSsoIdentityRepository;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\User\Contract\ValueObject\UserId;
use DateTimeImmutable;

/** @implements CommandHandler<LinkSsoIdentityCommand> */
final readonly class LinkSsoIdentityHandler implements CommandHandler
{
    public function __construct(
        private UserSsoIdentityRepository $userSsoIdentityRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $now = new DateTimeImmutable;
        $ssoConfigurationId = new SsoConfigurationId($command->configurationId);

        $userSsoIdentity = new UserSsoIdentity(
            id: new UserSsoIdentityId($command->id),
            userId: new UserId($command->userId),
            configurationId: $ssoConfigurationId,
            subject: $command->subject,
            emailAtLink: $command->emailAtLink,
            linkedAt: $now,
        );

        $this->userSsoIdentityRepository->create($userSsoIdentity);

        $this->eventCollector->collect(new SsoIdentityLinked(
            identityId: $userSsoIdentity->id->value,
            userId: $userSsoIdentity->userId->value,
            configurationId: $ssoConfigurationId->value,
            subject: $userSsoIdentity->subject,
            occurredAt: $now,
        ));
    }
}
