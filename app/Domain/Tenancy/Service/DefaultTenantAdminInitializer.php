<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Service;

use App\Application\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Domain\Authorization\Contract\Command\SeedDefaultRolesCommand;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Repository\RoleRepository;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RoleName;
use App\Domain\Tenancy\Contract\Service\TenantAdminInitializer;
use App\Domain\Tenancy\Contract\Service\TenantDefaultEmailTemplateSeeder;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\Service\TenantAdminUserSnapshotFactory;
use DateTimeImmutable;

final readonly class DefaultTenantAdminInitializer implements TenantAdminInitializer
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
    ) {}

    public function initialize(string $adminId, string $adminName, string $adminEmail): UserCreated
    {
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

        $user = $this->tenantAdminUserSnapshotFactory->createFromPrimitives($adminId, $adminName, $adminEmail);

        $this->userRepository->create($user);

        $this->userPermissionRepository->assignRole($adminId, $superAdminRole->id);

        return new UserCreated(
            userId: $adminId,
            name: $adminName,
            email: $adminEmail,
            occurredAt: new DateTimeImmutable,
        );
    }
}
