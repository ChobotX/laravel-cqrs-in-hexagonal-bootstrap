<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Tenancy\Contract\Command\MigrateAllTenantsCommand;
use App\Domain\Tenancy\Contract\Service\TenantProvisioner;

/** @implements CommandHandler<MigrateAllTenantsCommand> */
final readonly class MigrateAllTenantsHandler implements CommandHandler
{
    public function __construct(
        private TenantProvisioner $tenantProvisioner,
    ) {}

    public function handle(Command $command): void
    {
        $this->tenantProvisioner->migrateAllTenants();
    }
}
