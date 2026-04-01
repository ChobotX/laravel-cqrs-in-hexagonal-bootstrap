<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetAssignableRoles;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Contract\UserPermissionRepository;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleAssignmentPolicy;

/** @implements QueryHandler<GetAssignableRolesQuery, list<Role>> */
final readonly class GetAssignableRolesHandler implements QueryHandler
{
    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     */
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
        private RoleRepository $roleRepository,
        private PermissionResolver $permissionResolver,
        private RoleAssignmentPolicy $roleAssignmentPolicy,
        private array $availableModules,
    ) {}

    /** @return list<Role> */
    public function handle(Query $query): array
    {
        $roles = $this->userPermissionRepository->userRoles($query->assignerUserId);
        $overrides = $this->userPermissionRepository->userOverrides($query->assignerUserId);
        $assignerPermissions = $this->permissionResolver->resolve($roles, $overrides, $this->availableModules);

        $isSuperAdmin = array_any($assignerPermissions, fn ($p): bool => $p->source === PermissionResolver::SUPER_ADMIN_SOURCE);

        $allRoles = $this->roleRepository->findAll();

        return $this->roleAssignmentPolicy->assignableRoles(
            $assignerPermissions,
            $allRoles,
            $this->availableModules,
            $isSuperAdmin,
        );
    }
}
