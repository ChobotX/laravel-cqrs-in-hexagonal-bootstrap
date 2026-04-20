<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Command;

use App\Application\Event\PropertyChangeBuilder;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Sso\Constant\SsoConfigurationFields;
use App\Domain\Sso\Contract\Command\UpdateSsoConfigurationCommand;
use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\JitMode;
use App\Domain\Sso\Contract\Event\SsoConfigurationUpdated;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Repository\SsoConfigurationRepository;
use App\Domain\Sso\Contract\ValueObject\AllowedEmailDomains;
use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use DateTimeImmutable;

use function implode;

/** @implements CommandHandler<UpdateSsoConfigurationCommand> */
final readonly class UpdateSsoConfigurationHandler implements CommandHandler
{
    public function __construct(
        private SsoConfigurationRepository $ssoConfigurationRepository,
        private EventCollector $eventCollector,
        private PropertyChangeBuilder $propertyChangeBuilder,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->ssoConfigurationRepository->findById(new SsoConfigurationId($command->id));

        if (! $existing instanceof SsoConfiguration) {
            throw new SsoConfigurationNotFoundException($command->id);
        }

        $jitMode = JitMode::from($command->jitMode);
        $allowedEmailDomains = new AllowedEmailDomains($command->allowedEmailDomains);
        $now = new DateTimeImmutable;

        $ssoConfiguration = new SsoConfiguration(
            id: $existing->id,
            providerType: $existing->providerType,
            slug: $existing->slug,
            displayName: $command->displayName,
            enabled: $command->enabled,
            enforce: $command->enforce,
            jitMode: $jitMode,
            allowedEmailDomains: $allowedEmailDomains,
            config: $command->config,
            createdAt: $existing->createdAt,
            updatedAt: $now,
        );

        $changes = $this->propertyChangeBuilder->diff([
            SsoConfigurationFields::DISPLAY_NAME => [$existing->displayName, $ssoConfiguration->displayName],
            SsoConfigurationFields::ENABLED => [$existing->enabled, $ssoConfiguration->enabled],
            SsoConfigurationFields::ENFORCE => [$existing->enforce, $ssoConfiguration->enforce],
            SsoConfigurationFields::JIT_MODE => [$existing->jitMode->value, $ssoConfiguration->jitMode->value],
            SsoConfigurationFields::ALLOWED_EMAIL_DOMAINS => [
                implode(',', $existing->allowedEmailDomains->domains),
                implode(',', $ssoConfiguration->allowedEmailDomains->domains),
            ],
            SsoConfigurationFields::CONFIG_FINGERPRINT => [$this->fingerprint($existing->config), $this->fingerprint($ssoConfiguration->config)],
        ]);

        if ($changes === []) {
            return;
        }

        $this->ssoConfigurationRepository->update($ssoConfiguration);

        $this->eventCollector->collect(new SsoConfigurationUpdated(
            configurationId: $ssoConfiguration->id->value,
            changes: $changes,
            occurredAt: $now,
        ));
    }

    /** @param array<string, scalar|array<int|string, mixed>|null> $config */
    private function fingerprint(array $config): string
    {
        return hash('sha256', (string) json_encode($config));
    }
}
