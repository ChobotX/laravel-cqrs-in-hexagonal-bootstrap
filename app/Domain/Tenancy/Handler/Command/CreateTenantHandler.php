<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Tenancy\TenantProvisioner;
use App\Domain\Tenancy\Contract\Command\CreateTenantCommand;

/** @implements CommandHandler<CreateTenantCommand> */
final readonly class CreateTenantHandler implements CommandHandler
{
    public function __construct(
        private TenantProvisioner $tenantProvisioner,
    ) {}

    public function handle(Command $command): void
    {
        $this->tenantProvisioner->createTenant(
            $command->name,
            $command->slug,
            $command->domain,
        );
    }
}
