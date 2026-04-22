<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Contract\Attribute\SkipDomainEvent;
use App\Contract\Attribute\SkipTransaction;
use App\Contract\Bus\CommandBus;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\IdGenerator;
use App\Domain\Authorization\Contract\Command\AssignRoleToUserCommand;
use App\Domain\Authorization\Contract\Command\CreateSystemRoleCommand;
use App\Domain\Authorization\Contract\Command\SeedDefaultRolesCommand;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Contract\Service\TenantBootstrapper;
use App\Domain\Tenancy\Contract\Service\TenantDefaultEmailTemplateSeeder;
use App\Domain\User\Contract\Command\CreateUserCommand;

/** @implements CommandHandler<InitializeTenantAdminCommand> */
#[SkipTransaction(reason: 'Bootstraps tenant schema — DDL-adjacent initialization')]
#[SkipDomainEvent(reason: 'Orchestrator — inner CreateUserCommand handler emits UserCreated')]
final readonly class InitializeTenantAdminHandler implements CommandHandler
{
    private const string SUPER_ADMIN_NAME = 'Super Admin';

    private const string SUPER_ADMIN_DESCRIPTION = 'System super admin with all permissions';

    public function __construct(
        private TenantBootstrapper $tenantBootstrapper,
        private TenantDefaultEmailTemplateSeeder $tenantDefaultEmailTemplateSeeder,
        private CommandBus $commandBus,
        private IdGenerator $idGenerator,
    ) {}

    public function handle(Command $command): void
    {
        $this->tenantBootstrapper->bootstrapBySlug($command->tenantSlug);

        $this->tenantDefaultEmailTemplateSeeder->seed();
        $this->commandBus->dispatch(new SeedDefaultRolesCommand);

        $superAdminRoleId = $this->idGenerator->generate();

        $this->commandBus->dispatch(new CreateSystemRoleCommand(
            id: $superAdminRoleId,
            name: self::SUPER_ADMIN_NAME,
            description: self::SUPER_ADMIN_DESCRIPTION,
        ));

        $this->commandBus->dispatch(new CreateUserCommand(
            id: $command->adminId,
            name: $command->adminName,
            email: $command->adminEmail,
        ));

        $this->commandBus->dispatch(new AssignRoleToUserCommand(
            userId: $command->adminId,
            roleId: $superAdminRoleId,
        ));

        // No reset: DispatchCollectedEvents middleware runs after this handler
        // and needs the tenant context active to queue the UserCreated event
        // into the correct tenant's jobs table. Context is scoped per request.
    }
}
