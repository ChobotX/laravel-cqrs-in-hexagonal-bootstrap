<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Application\Bus\SkipDomainEvent;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Tenancy\TenantBootstrapper;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Contract\Service\TenantAdminInitializer;

/** @implements CommandHandler<InitializeTenantAdminCommand> */
#[SkipDomainEvent(reason: 'Events collected by TenantAdminInitializer, flushed by DispatchCollectedEvents middleware')]
#[SkipTransaction(reason: 'Bootstraps tenant schema — DDL-adjacent initialization')]
final readonly class InitializeTenantAdminHandler implements CommandHandler
{
    public function __construct(
        private TenantAdminInitializer $tenantAdminInitializer,
        private TenantBootstrapper $tenantBootstrapper,
    ) {}

    public function handle(Command $command): void
    {
        $this->tenantBootstrapper->bootstrapBySlug($command->tenantSlug);

        $this->tenantAdminInitializer->initialize(
            $command->adminId,
            $command->adminName,
            $command->adminEmail,
        );

        // No reset: DispatchCollectedEvents middleware runs after this handler
        // and needs the tenant context active to queue the UserCreated event
        // into the correct tenant's jobs table. Context is scoped per request.
    }
}
