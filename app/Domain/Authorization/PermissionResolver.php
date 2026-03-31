<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

final readonly class PermissionResolver
{
    public const string SUPER_ADMIN_SOURCE = 'system:super-admin';

    private ModuleConfigExpander $moduleConfigExpander;

    public function __construct()
    {
        $this->moduleConfigExpander = new ModuleConfigExpander;
    }

    /**
     * @param  list<Role>  $roles
     * @param  list<UserPermissionOverride>  $overrides
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @return list<EffectivePermission>
     */
    public function resolve(array $roles, array $overrides, array $availableModules): array
    {
        if ($this->hasSuperAdminRole($roles)) {
            return $this->grantAll($availableModules);
        }

        $roleGrants = $this->expandRolePermissions($roles, $availableModules);
        $expandedOverrides = $this->expandOverrides($overrides, $availableModules);

        return $this->mergePermissions($roleGrants, $expandedOverrides, $availableModules);
    }

    /** @param list<Role> $roles */
    private function hasSuperAdminRole(array $roles): bool
    {
        return array_any($roles, fn ($role) => $role->isSystem);
    }

    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @return list<EffectivePermission>
     */
    private function grantAll(array $availableModules): array
    {
        $permissions = [];

        foreach ($this->moduleConfigExpander->allLeafKeys($availableModules) as [$moduleName, $featureName, $actionName]) {
            $permissions[] = new EffectivePermission(
                permissionKey: new PermissionKey(
                    new Module($moduleName),
                    new Feature($featureName),
                    Action::from($actionName),
                ),
                granted: true,
                scope: AccessScope::All,
                source: self::SUPER_ADMIN_SOURCE,
            );
        }

        return $permissions;
    }

    /**
     * @param  list<Role>  $roles
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @return array<string, array{scope: AccessScope, source: string}>
     */
    private function expandRolePermissions(array $roles, array $availableModules): array
    {
        $grants = [];

        foreach ($roles as $role) {
            $this->applyRoleGrants($role, $availableModules, $grants);
        }

        return $grants;
    }

    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @param  array<string, array{scope: AccessScope, source: string}>  $grants
     */
    private function applyRoleGrants(Role $role, array $availableModules, array &$grants): void
    {
        foreach ($role->permissions as $rolePermission) {
            $leafKeys = $this->moduleConfigExpander->expandToLeaves($rolePermission->permissionKey, $availableModules);

            foreach ($leafKeys as $leafKey) {
                $existing = $grants[$leafKey] ?? null;

                if ($existing === null || $rolePermission->scope->isMorePermissiveThan($existing['scope'])) {
                    $grants[$leafKey] = [
                        'scope' => $rolePermission->scope,
                        'source' => 'role:'.$role->name->value,
                    ];
                }
            }
        }
    }

    /**
     * @param  list<UserPermissionOverride>  $overrides
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @return array<string, array{type: OverrideType, scope: AccessScope}>
     */
    private function expandOverrides(array $overrides, array $availableModules): array
    {
        $expanded = [];

        foreach ($overrides as $override) {
            $this->applyOverride($override, $availableModules, $expanded);
        }

        return $expanded;
    }

    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @param  array<string, array{type: OverrideType, scope: AccessScope}>  $expanded
     */
    private function applyOverride(UserPermissionOverride $userPermissionOverride, array $availableModules, array &$expanded): void
    {
        $leafKeys = $this->moduleConfigExpander->expandToLeaves($userPermissionOverride->permissionKey, $availableModules);

        foreach ($leafKeys as $leafKey) {
            $existing = $expanded[$leafKey] ?? null;

            if ($existing !== null && $existing['type'] === OverrideType::Deny) {
                continue;
            }

            if ($userPermissionOverride->type === OverrideType::Deny) {
                $expanded[$leafKey] = ['type' => OverrideType::Deny, 'scope' => $userPermissionOverride->scope];

                continue;
            }

            if ($existing === null || $userPermissionOverride->scope->isMorePermissiveThan($existing['scope'])) {
                $expanded[$leafKey] = ['type' => OverrideType::Grant, 'scope' => $userPermissionOverride->scope];
            }
        }
    }

    /**
     * @param  array<string, array{scope: AccessScope, source: string}>  $roleGrants
     * @param  array<string, array{type: OverrideType, scope: AccessScope}>  $expandedOverrides
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $availableModules
     * @return list<EffectivePermission>
     */
    private function mergePermissions(array $roleGrants, array $expandedOverrides, array $availableModules): array
    {
        $permissions = [];

        foreach ($this->moduleConfigExpander->allLeafKeys($availableModules) as [$moduleName, $featureName, $actionName]) {
            $key = $moduleName.'.'.$featureName.'.'.$actionName;
            $permissionKey = new PermissionKey(
                new Module($moduleName),
                new Feature($featureName),
                Action::from($actionName),
            );

            $permissions[] = $this->resolveLeafPermission(
                $permissionKey,
                $expandedOverrides[$key] ?? null,
                $roleGrants[$key] ?? null,
            );
        }

        return $permissions;
    }

    /**
     * @param  array{type: OverrideType, scope: AccessScope}|null  $override
     * @param  array{scope: AccessScope, source: string}|null  $roleGrant
     */
    private function resolveLeafPermission(PermissionKey $permissionKey, ?array $override, ?array $roleGrant): EffectivePermission
    {
        if ($override !== null && $override['type'] === OverrideType::Deny) {
            return new EffectivePermission($permissionKey, false, $override['scope'], 'user:deny');
        }

        if ($override !== null && $override['type'] === OverrideType::Grant) {
            return new EffectivePermission($permissionKey, true, $override['scope'], 'user:grant');
        }

        if ($roleGrant !== null) {
            return new EffectivePermission($permissionKey, true, $roleGrant['scope'], $roleGrant['source']);
        }

        return new EffectivePermission($permissionKey, false, AccessScope::All, '');
    }
}
