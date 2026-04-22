<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\EmailTemplate\Contract\Command\SeedDefaultEmailTemplatesCommand;
use App\Domain\Tenancy\Contract\Command\CreateTenantCommand;
use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;
use App\Domain\Tenancy\Contract\Service\TenantProvisioner;

/** @implements CommandHandler<CreateTenantCommand> */
#[SkipDomainEvent(reason: 'Infrastructure provisioning — creates tenant schema, no domain state change')]
final readonly class CreateTenantHandler implements CommandHandler
{
    public function __construct(
        private TenantProvisioner $tenantProvisioner,
        private TenantBootstrapper $tenantBootstrapper,
        private CommandBus $commandBus,
    ) {}

    public function handle(Command $command): void
    {
        $this->tenantProvisioner->createTenant(
            $command->slug,
            $command->domain,
        );

        $this->tenantProvisioner->migrateTenant($command->slug, $command->name);

        $this->tenantBootstrapper->bootstrapBySlug($command->slug);

        try {
            $this->commandBus->dispatch(new SeedDefaultEmailTemplatesCommand);
        } finally {
            $this->tenantBootstrapper->reset();
            $this->tenantProvisioner->resetTenantPersistenceScope();
        }
    }
}
