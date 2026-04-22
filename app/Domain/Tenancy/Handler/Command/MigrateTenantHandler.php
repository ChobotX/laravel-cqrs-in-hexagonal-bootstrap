<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Tenancy\Contract\Command\MigrateTenantCommand;
use App\Domain\Tenancy\Contract\Service\TenantProvisioner;

/** @implements CommandHandler<MigrateTenantCommand> */
#[SkipDomainEvent(reason: 'Infrastructure provisioning — runs tenant migrations, no domain state change')]
final readonly class MigrateTenantHandler implements CommandHandler
{
    public function __construct(
        private TenantProvisioner $tenantProvisioner,
    ) {}

    public function handle(Command $command): void
    {
        $this->tenantProvisioner->migrateTenant($command->slug);
        $this->tenantProvisioner->resetTenantPersistenceScope();
    }
}
