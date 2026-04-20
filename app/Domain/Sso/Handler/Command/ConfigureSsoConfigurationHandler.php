<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Sso\Contract\Command\ConfigureSsoConfigurationCommand;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Event\SsoConfigurationCreated;
use App\Domain\Sso\Contract\Exception\SsoConfigurationConflictException;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\ValueObject\SsoSlug;
use DateTimeImmutable;

/** @implements CommandHandler<ConfigureSsoConfigurationCommand> */
final readonly class ConfigureSsoConfigurationHandler implements CommandHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $providerType = ProviderType::from($command->providerType);
        $ssoSlug = new SsoSlug($command->slug);

        if ($this->ssoConfigurationRepository->findBySlug($providerType, $ssoSlug->value) instanceof SsoConfiguration) {
            throw new SsoConfigurationConflictException($providerType->value, $ssoSlug->value);
        }

        $now = new DateTimeImmutable;

        $ssoConfiguration = new SsoConfiguration(
            id: new SsoConfigurationId($command->id),
            providerType: $providerType,
            slug: $ssoSlug->value,
            displayName: $command->displayName,
            enabled: $command->enabled,
            enforce: $command->enforce,
            jitMode: JitMode::from($command->jitMode),
            allowedEmailDomains: new AllowedEmailDomains($command->allowedEmailDomains),
            config: $command->config,
            createdAt: $now,
            updatedAt: $now,
        );

        $this->ssoConfigurationRepository->create($ssoConfiguration);

        $this->eventCollector->collect(new SsoConfigurationCreated(
            configurationId: $ssoConfiguration->id->value,
            providerType: $providerType->value,
            slug: $ssoSlug->value,
            displayName: $ssoConfiguration->displayName,
            enabled: $ssoConfiguration->enabled,
            occurredAt: $now,
        ));
    }
}
