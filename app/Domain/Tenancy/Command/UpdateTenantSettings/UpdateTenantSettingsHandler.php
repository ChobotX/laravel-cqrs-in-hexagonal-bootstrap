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
use App\Domain\Tenancy\Exception\InvalidTenantDisplayTimezoneException;
use App\Domain\Tenancy\Exception\InvalidTenantNameException;
use App\Domain\Tenancy\Exception\TenantNotFoundException;
use DateTimeImmutable;
use SplFileInfo;

use function in_array;
use function timezone_identifiers_list;
use function trim;

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

        $displayTimezone = $this->normalizeIncomingDisplayTimezone($command->displayTimezone);

        $changes = $this->propertyChangeBuilder->diff([
            'name' => [$existing->name, $command->name],
            'logo' => [$existing->logoUrl, $this->resolveNewLogo($existing, $command)],
            'display_timezone' => [$existing->displayTimezone, $displayTimezone],
        ]);

        if ($changes === []) {
            return;
        }

        $this->tenantSettingsRepository->updateSettings(
            $command->tenantId,
            $command->name,
            $command->logo,
            $command->removeLogo,
            $displayTimezone,
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

    private function normalizeIncomingDisplayTimezone(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $trimmed = trim($raw);

        if ($trimmed === '') {
            return null;
        }

        if (! in_array($trimmed, timezone_identifiers_list(), true)) {
            throw new InvalidTenantDisplayTimezoneException($trimmed);
        }

        return $trimmed;
    }
}
