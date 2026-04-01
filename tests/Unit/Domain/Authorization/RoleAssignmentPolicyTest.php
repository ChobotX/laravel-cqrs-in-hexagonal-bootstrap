<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\EffectivePermission;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleAssignmentPolicy;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermission;

/** @return array<string, array{features: array<string, array{actions: list<string>}>}> */
function policyModules(): array
{
    return [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create', 'update', 'delete']],
                'roles' => ['actions' => ['read', 'update']],
            ],
        ],
    ];
}

/** @param array<string, string> $permissions */
function policyRole(string $name, array $permissions, bool $isSystem = false): Role
{
    $rolePermissions = [];

    foreach ($permissions as $permKey => $scope) {
        $parts = explode('.', $permKey);
        $module = new Module($parts[0]);
        $feature = isset($parts[1]) ? new Feature($parts[1]) : null;
        $action = isset($parts[2]) ? Action::from($parts[2]) : null;

        $rolePermissions[] = new RolePermission(
            new PermissionKey($module, $feature, $action),
            AccessScope::from($scope),
        );
    }

    return new Role(
        id: new RoleId('550e8400-e29b-41d4-a716-'.substr(str_pad(dechex(crc32($name) & 0xFFFFFFFF), 12, '0', STR_PAD_LEFT), 0, 12)),
        name: new RoleName($name),
        description: $name.' role',
        isSystem: $isSystem,
        permissions: $rolePermissions,
    );
}

/**
 * @param  array<string, string>  $granted
 * @return list<EffectivePermission>
 */
function policyPermissions(array $granted): array
{
    $permissions = [];

    foreach ($granted as $key => $scope) {
        $parts = explode('.', $key);
        $permissions[] = new EffectivePermission(
            new PermissionKey(
                new Module($parts[0]),
                isset($parts[1]) ? new Feature($parts[1]) : null,
                isset($parts[2]) ? Action::from($parts[2]) : null,
            ),
            true,
            AccessScope::from($scope),
            'role:Test',
        );
    }

    return $permissions;
}

it('super admin can assign all roles including system', function (): void {
    $policy = new RoleAssignmentPolicy;

    $systemRole = policyRole('Super Admin', [], true);
    $regularRole = policyRole('Editor', ['users.list.read' => 'all']);

    $result = $policy->assignableRoles([], [$systemRole, $regularRole], policyModules(), true);

    expect($result)->toHaveCount(2);
});

it('non-super-admin cannot assign system roles', function (): void {
    $policy = new RoleAssignmentPolicy;

    $systemRole = policyRole('Super Admin', [], true);
    $regularRole = policyRole('Viewer', ['users.list.read' => 'all']);

    $assignerPermissions = policyPermissions([
        'users.list.read' => 'all',
        'users.list.create' => 'all',
        'users.list.update' => 'all',
        'users.list.delete' => 'all',
        'users.roles.read' => 'all',
        'users.roles.update' => 'all',
    ]);

    $result = $policy->assignableRoles($assignerPermissions, [$systemRole, $regularRole], policyModules(), false);

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Viewer');
});

it('non-super-admin can assign role whose permissions are subset', function (): void {
    $policy = new RoleAssignmentPolicy;

    $viewerRole = policyRole('Viewer', ['users.list.read' => 'all']);

    $assignerPermissions = policyPermissions([
        'users.list.read' => 'all',
        'users.list.create' => 'all',
        'users.list.update' => 'all',
        'users.list.delete' => 'all',
        'users.roles.read' => 'all',
        'users.roles.update' => 'all',
    ]);

    $result = $policy->assignableRoles($assignerPermissions, [$viewerRole], policyModules(), false);

    expect($result)->toHaveCount(1);
});

it('non-super-admin cannot assign role with permission they lack', function (): void {
    $policy = new RoleAssignmentPolicy;

    $editorRole = policyRole('Editor', [
        'users.list.read' => 'all',
        'users.list.delete' => 'all',
    ]);

    $assignerPermissions = policyPermissions([
        'users.list.read' => 'all',
        'users.roles.read' => 'all',
        'users.roles.update' => 'all',
    ]);

    $result = $policy->assignableRoles($assignerPermissions, [$editorRole], policyModules(), false);

    expect($result)->toHaveCount(0);
});

it('non-super-admin cannot assign role with wider scope on same permission', function (): void {
    $policy = new RoleAssignmentPolicy;

    $roleWithAllScope = policyRole('Wide', ['users.list.read' => 'all']);

    $assignerPermissions = policyPermissions([
        'users.list.read' => 'team',
        'users.roles.read' => 'all',
        'users.roles.update' => 'all',
    ]);

    $result = $policy->assignableRoles($assignerPermissions, [$roleWithAllScope], policyModules(), false);

    expect($result)->toHaveCount(0);
});

it('empty permissions user can only assign roles with no permissions', function (): void {
    $policy = new RoleAssignmentPolicy;

    $emptyRole = policyRole('Empty', []);
    $viewerRole = policyRole('Viewer', ['users.list.read' => 'all']);

    $result = $policy->assignableRoles([], [$emptyRole, $viewerRole], policyModules(), false);

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Empty');
});
