<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Handler\Command;

use App\Application\Bus\CommandBus;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\Command;
use App\Contract\Command\CommandHandler;
use App\Contract\Event\EventCollector;
use App\Contract\IdGenerator;
use App\Contract\Tenancy\TenantBootstrapper;
use App\Domain\Authorization\Contract\Command\SeedDefaultRolesCommand;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RoleName;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Contract\Service\TenantDefaultEmailTemplateSeeder;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\TenantAdminUserSnapshotFactory;
use DateTimeImmutable;

/** @implements CommandHandler<InitializeTenantAdminCommand> */
#[SkipTransaction(reason: 'Bootstraps tenant schema — DDL-adjacent initialization')]
final readonly class InitializeTenantAdminHandler implements CommandHandler
{
    private const string SUPER_ADMIN_NAME = 'Super Admin';

    private const string SUPER_ADMIN_DESCRIPTION = 'System super admin with all permissions';

    public function __construct(
        private TenantDefaultEmailTemplateSeeder $tenantDefaultEmailTemplateSeeder,
        private CommandBus $commandBus,
        private RoleRepository $roleRepository,
        private UserRepository $userRepository,
        private UserPermissionRepository $userPermissionRepository,
        private IdGenerator $idGenerator,
        private TenantAdminUserSnapshotFactory $tenantAdminUserSnapshotFactory,
        private TenantBootstrapper $tenantBootstrapper,
        private EventCollector $eventCollector,
    ) {}

    public function handle(Command $command): void
    {
        $this->tenantBootstrapper->bootstrapBySlug($command->tenantSlug);

        $this->tenantDefaultEmailTemplateSeeder->seed();
        $this->commandBus->dispatch(new SeedDefaultRolesCommand);

        $superAdminRole = new Role(
            id: new RoleId($this->idGenerator->generate()),
            name: new RoleName(self::SUPER_ADMIN_NAME),
            description: self::SUPER_ADMIN_DESCRIPTION,
            isSystem: true,
            permissions: [],
        );

        $this->roleRepository->create($superAdminRole);

        $adminUser = $this->tenantAdminUserSnapshotFactory->createFromPrimitives(
            $command->adminId,
            $command->adminName,
            $command->adminEmail,
        );
        $this->userRepository->create($adminUser);
        $this->userPermissionRepository->assignRole($command->adminId, $superAdminRole->id);

        $this->eventCollector->collect(new UserCreated(
            userId: $command->adminId,
            name: $command->adminName,
            email: $command->adminEmail,
            occurredAt: new DateTimeImmutable,
        ));

        // No reset: DispatchCollectedEvents middleware runs after this handler
        // and needs the tenant context active to queue the UserCreated event
        // into the correct tenant's jobs table. Context is scoped per request.
    }
}
