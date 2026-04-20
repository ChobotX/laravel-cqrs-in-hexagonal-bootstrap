<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Sso\Contract\Command\UnlinkSsoIdentityCommand;
use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\Event\SsoIdentityUnlinked;
use App\Domain\Sso\Contract\Repository\UserSsoIdentityRepository;
use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\Sso\Exception\SsoIdentityNotFoundException;
use DateTimeImmutable;

/** @implements CommandHandler<UnlinkSsoIdentityCommand> */
final readonly class UnlinkSsoIdentityHandler implements CommandHandler
{
    public function __construct(
        private UserSsoIdentityRepository $userSsoIdentityRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $userSsoIdentityId = new UserSsoIdentityId($command->id);
        $existing = $this->userSsoIdentityRepository->findById($userSsoIdentityId);

        if (! $existing instanceof UserSsoIdentity) {
            throw new SsoIdentityNotFoundException($command->id);
        }

        $this->userSsoIdentityRepository->delete($userSsoIdentityId);

        $this->eventCollector->collect(new SsoIdentityUnlinked(
            identityId: $userSsoIdentityId->value,
            userId: $existing->userId->value,
            configurationId: $existing->configurationId->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
