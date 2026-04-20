<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Sso\Contract\Command\DeleteSsoConfigurationCommand;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Event\SsoConfigurationDeleted;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\Repository\UserSsoIdentityRepository;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use DateTimeImmutable;

/** @implements CommandHandler<DeleteSsoConfigurationCommand> */
final readonly class DeleteSsoConfigurationHandler implements CommandHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
        private UserSsoIdentityRepository $userSsoIdentityRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $ssoConfigurationId = new SsoConfigurationId($command->id);
        $existing = $this->ssoConfigurationRepository->findById($ssoConfigurationId);

        if (! $existing instanceof SsoConfiguration) {
            throw new SsoConfigurationNotFoundException($command->id);
        }

        $this->userSsoIdentityRepository->deleteAllForConfiguration($ssoConfigurationId);
        $this->ssoConfigurationRepository->delete($ssoConfigurationId);

        $this->eventCollector->collect(new SsoConfigurationDeleted(
            configurationId: $ssoConfigurationId->value,
            occurredAt: new DateTimeImmutable,
        ));
    }
}
