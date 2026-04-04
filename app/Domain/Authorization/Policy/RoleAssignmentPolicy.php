<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Policy;

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\ValueObject\EffectivePermission;
use App\Domain\Authorization\Service\ModuleConfigExpander;

final readonly class RoleAssignmentPolicy
{
    private ModuleConfigExpander $moduleConfigExpander;

    public function __construct()
    {
        $this->moduleConfigExpander = new ModuleConfigExpander;
    }

    /**
     * @param  list<EffectivePermission>  $assignerPermissions
     * @param  list<Role>  $candidateRoles
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @return list<Role>
     */
    public function assignableRoles(array $assignerPermissions, array $candidateRoles, array $availableModules, bool $isSuperAdmin): array
    {
        if ($isSuperAdmin) {
            return $candidateRoles;
        }

        $grantedMap = $this->buildGrantedMap($assignerPermissions);

        return array_values(array_filter(
            $candidateRoles,
            fn (Role $role): bool => ! $role->isSystem && $this->isSuperset($grantedMap, $role, $availableModules),
        ));
    }

    /**
     * @param  list<EffectivePermission>  $permissions
     * @return array<string, AccessScope>
     */
    private function buildGrantedMap(array $permissions): array
    {
        $map = [];

        foreach ($permissions as $permission) {
            if ($permission->granted) {
                $map[(string) $permission->permissionKey] = $permission->scope;
            }
        }

        return $map;
    }

    /**
     * @param  array<string, AccessScope>  $grantedMap
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     */
    private function isSuperset(array $grantedMap, Role $role, array $availableModules): bool
    {
        foreach ($role->permissions as $rolePermission) {
            $leafKeys = $this->moduleConfigExpander->expandToLeaves($rolePermission->permissionKey, $availableModules);

            foreach ($leafKeys as $leafKey) {
                $assignerScope = $grantedMap[$leafKey] ?? null;

                if ($assignerScope === null) {
                    return false;
                }

                if ($rolePermission->scope->isMorePermissiveThan($assignerScope)) {
                    return false;
                }
            }
        }

        return true;
    }
}
