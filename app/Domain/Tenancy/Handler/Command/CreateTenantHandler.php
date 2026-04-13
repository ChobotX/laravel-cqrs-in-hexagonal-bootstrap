<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Tenancy\Contract\Command\CreateTenantCommand;
use App\Domain\Tenancy\Contract\Service\TenantProvisioner;

/** @implements CommandHandler<CreateTenantCommand> */
#[SkipDomainEvent(reason: 'Infrastructure provisioning — creates tenant schema, no domain state change')]
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

        $this->tenantProvisioner->migrateTenant($command->slug);
    }
}
