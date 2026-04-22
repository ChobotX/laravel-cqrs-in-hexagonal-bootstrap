<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Domain\Tenancy\Contract\Command\CreateTenantCommand;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Contract\Command\RegisterTenantWithAdminCommand;

/** @implements CommandHandler<RegisterTenantWithAdminCommand> */
#[SkipDomainEvent(reason: 'Orchestrator — inner CreateTenant and InitializeTenantAdmin handlers emit events')]
final readonly class RegisterTenantWithAdminHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function handle(Command $command): void
    {
        $this->commandBus->dispatch(new CreateTenantCommand(
            name: $command->name,
            slug: $command->slug,
            domain: $command->domain,
        ));

        $this->commandBus->dispatch(new InitializeTenantAdminCommand(
            tenantSlug: $command->slug,
            adminId: $command->adminId,
            adminName: $command->adminName,
            adminEmail: $command->adminEmail,
        ));
    }
}
