<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Command\UpdateTenantSettings;

use App\Application\Event\PropertyChangeBuilder;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Domain\Tenancy\Contract\Command\UpdateTenantSettingsCommand;
use App\Domain\Tenancy\Contract\Event\TenantSettingsUpdated;
use App\Domain\Tenancy\Contract\Repository\TenantSettingsRepository;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use App\Domain\Tenancy\Exception\InvalidTenantNameException;
use App\Domain\Tenancy\Exception\TenantNotFoundException;
use DateTimeImmutable;
use SplFileInfo;

/** @implements CommandHandler<UpdateTenantSettingsCommand> */
final readonly class UpdateTenantSettingsHandler implements CommandHandler
{
    public function __construct(
        private TenantSettingsRepository $tenantSettingsRepository,
        private EventCollector $eventCollector,
        private PropertyChangeBuilder $propertyChangeBuilder,
    ) {}

    public function handle(Command $command): void
    {
        $existing = $this->tenantSettingsRepository->findByTenantId($command->tenantId);

        if (! $existing instanceof TenantSettings) {
            throw new TenantNotFoundException($command->tenantId);
        }

        if (trim($command->name) === '') {
            throw new InvalidTenantNameException;
        }

        $changes = $this->propertyChangeBuilder->diff([
            'name' => [$existing->name, $command->name],
            'logo' => [$existing->logoUrl, $this->resolveNewLogo($existing, $command)],
        ]);

        if ($changes === []) {
            return;
        }

        $this->tenantSettingsRepository->updateSettings(
            $command->tenantId,
            $command->name,
            $command->logo,
            $command->removeLogo,
        );

        $this->eventCollector->collect(new TenantSettingsUpdated(
            tenantId: $command->tenantId,
            changes: $changes,
            occurredAt: new DateTimeImmutable,
        ));
    }

    private function resolveNewLogo(TenantSettings $tenantSettings, UpdateTenantSettingsCommand $updateTenantSettingsCommand): ?string
    {
        if ($updateTenantSettingsCommand->removeLogo) {
            return null;
        }

        if ($updateTenantSettingsCommand->logo instanceof SplFileInfo) {
            return 'updated';
        }

        return $tenantSettings->logoUrl;
    }
}
