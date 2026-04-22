<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\EmailTemplate\Contract\Command\SeedDefaultEmailTemplatesCommand;
use App\Domain\Tenancy\Contract\Command\MigrateTenantCommand;
use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;
use App\Domain\Tenancy\Contract\Service\TenantProvisioner;

/** @implements CommandHandler<MigrateTenantCommand> */
#[SkipDomainEvent(reason: 'Infrastructure provisioning — runs tenant migrations, no domain state change')]
final readonly class MigrateTenantHandler implements CommandHandler
{
    public function __construct(
        private TenantProvisioner $tenantProvisioner,
        private TenantBootstrapper $tenantBootstrapper,
        private CommandBus $commandBus,
    ) {}

    public function handle(Command $command): void
    {
        $this->tenantProvisioner->migrateTenant($command->slug);

        $this->tenantBootstrapper->bootstrapBySlug($command->slug);

        try {
            // Backfill default email templates so existing tenants pick up new types/locales after migration.
            $this->commandBus->dispatch(new SeedDefaultEmailTemplatesCommand);
        } finally {
            $this->tenantBootstrapper->reset();
            $this->tenantProvisioner->resetTenantPersistenceScope();
        }
    }
}
