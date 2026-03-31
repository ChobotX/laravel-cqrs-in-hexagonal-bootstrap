<?php

declare(strict_types=1);

use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\EffectivePermission;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use App\Domain\Authorization\RolePermission;
use App\Domain\Authorization\UserPermissionOverride;

/** @return array<string, array{features: array<string, array{actions: list<string>}>}> */
function modules(): array
{
    return [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create', 'update', 'delete']],
            ],
        ],
        'crm' => [
            'features' => [
                'contacts' => ['actions' => ['read', 'create', 'update', 'delete']],
                'deals' => ['actions' => ['read', 'create', 'update', 'delete']],
            ],
        ],
    ];
}

/** @param array<string, string> $permissions */
function makeRole(string $name, array $permissions, bool $isSystem = false): Role
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

function makeOverride(string $permKey, OverrideType $overrideType, AccessScope $accessScope = AccessScope::All): UserPermissionOverride
{
    $parts = explode('.', $permKey);
    $module = new Module($parts[0]);
    $feature = isset($parts[1]) ? new Feature($parts[1]) : null;
    $action = isset($parts[2]) ? Action::from($parts[2]) : null;

    return new UserPermissionOverride(
        new PermissionKey($module, $feature, $action),
        $overrideType,
        $accessScope,
    );
}

/** @param list<EffectivePermission> $permissions */
function findPermission(array $permissions, string $key): ?EffectivePermission
{
    foreach ($permissions as $permission) {
        if ((string) $permission->permissionKey === $key) {
            return $permission;
        }
    }

    return null;
}

it('returns all permissions as not granted when no roles and no overrides', function (): void {
    $resolver = new PermissionResolver;

    $result = $resolver->resolve([], [], modules());

    expect($result)->toHaveCount(12);

    foreach ($result as $permission) {
        expect($permission->granted)->toBeFalse();
    }
});

it('grants all permissions with all scope for super admin role', function (): void {
    $resolver = new PermissionResolver;

    $superAdmin = new Role(
        id: new RoleId('550e8400-e29b-41d4-a716-446655440001'),
        name: new RoleName('Super Admin'),
        description: 'Super Admin',
        isSystem: true,
        permissions: [],
    );

    $result = $resolver->resolve([$superAdmin], [], modules());

    expect($result)->toHaveCount(12);

    foreach ($result as $permission) {
        expect($permission->granted)->toBeTrue()
            ->and($permission->scope)->toBe(AccessScope::All)
            ->and($permission->source)->toBe(PermissionResolver::SUPER_ADMIN_SOURCE);
    }
});

it('expands module-level permission to all features and actions', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Editor', ['users' => 'all']);
    $result = $resolver->resolve([$role], [], modules());

    $usersRead = findPermission($result, 'users.list.read');
    $usersCreate = findPermission($result, 'users.list.create');
    $usersUpdate = findPermission($result, 'users.list.update');
    $usersDelete = findPermission($result, 'users.list.delete');
    $crmRead = findPermission($result, 'crm.contacts.read');

    assert($usersRead instanceof EffectivePermission);
    assert($usersCreate instanceof EffectivePermission);
    assert($usersUpdate instanceof EffectivePermission);
    assert($usersDelete instanceof EffectivePermission);
    assert($crmRead instanceof EffectivePermission);

    expect($usersRead->granted)->toBeTrue()
        ->and($usersRead->scope)->toBe(AccessScope::All)
        ->and($usersCreate->granted)->toBeTrue()
        ->and($usersUpdate->granted)->toBeTrue()
        ->and($usersDelete->granted)->toBeTrue()
        ->and($crmRead->granted)->toBeFalse();
});

it('expands feature-level permission to all actions', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Editor', ['crm.contacts' => 'all']);
    $result = $resolver->resolve([$role], [], modules());

    $contactsRead = findPermission($result, 'crm.contacts.read');
    $contactsCreate = findPermission($result, 'crm.contacts.create');
    $dealsRead = findPermission($result, 'crm.deals.read');

    assert($contactsRead instanceof EffectivePermission);
    assert($contactsCreate instanceof EffectivePermission);
    assert($dealsRead instanceof EffectivePermission);

    expect($contactsRead->granted)->toBeTrue()
        ->and($contactsCreate->granted)->toBeTrue()
        ->and($dealsRead->granted)->toBeFalse();
});

it('handles action-level permission without expansion', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Viewer', ['users.list.read' => 'all']);
    $result = $resolver->resolve([$role], [], modules());

    $usersRead = findPermission($result, 'users.list.read');
    $usersCreate = findPermission($result, 'users.list.create');

    assert($usersRead instanceof EffectivePermission);
    assert($usersCreate instanceof EffectivePermission);

    expect($usersRead->granted)->toBeTrue()
        ->and($usersCreate->granted)->toBeFalse();
});

it('merges permissions from multiple roles using most permissive scope', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Viewer', ['users.list.read' => 'own']);
    $member = makeRole('Member', ['users.list.read' => 'team']);

    $result = $resolver->resolve([$role, $member], [], modules());

    $usersRead = findPermission($result, 'users.list.read');

    assert($usersRead instanceof EffectivePermission);

    expect($usersRead->granted)->toBeTrue()
        ->and($usersRead->scope)->toBe(AccessScope::Team);
});

it('applies deny override over role grant', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Admin', ['users' => 'all']);
    $userPermissionOverride = makeOverride('users.list.delete', OverrideType::Deny);

    $result = $resolver->resolve([$role], [$userPermissionOverride], modules());

    $usersRead = findPermission($result, 'users.list.read');
    $usersDelete = findPermission($result, 'users.list.delete');

    assert($usersRead instanceof EffectivePermission);
    assert($usersDelete instanceof EffectivePermission);

    expect($usersRead->granted)->toBeTrue()
        ->and($usersDelete->granted)->toBeFalse()
        ->and($usersDelete->source)->toBe('user:deny');
});

it('applies grant override when no role grant exists', function (): void {
    $resolver = new PermissionResolver;

    $userPermissionOverride = makeOverride('crm.contacts.read', OverrideType::Grant, AccessScope::Own);

    $result = $resolver->resolve([], [$userPermissionOverride], modules());

    $contactsRead = findPermission($result, 'crm.contacts.read');
    $contactsCreate = findPermission($result, 'crm.contacts.create');

    assert($contactsRead instanceof EffectivePermission);
    assert($contactsCreate instanceof EffectivePermission);

    expect($contactsRead->granted)->toBeTrue()
        ->and($contactsRead->scope)->toBe(AccessScope::Own)
        ->and($contactsRead->source)->toBe('user:grant')
        ->and($contactsCreate->granted)->toBeFalse();
});

it('deny override wins over grant override at same level', function (): void {
    $resolver = new PermissionResolver;

    $userPermissionOverride = makeOverride('users.list.read', OverrideType::Grant);
    $denyOverride = makeOverride('users.list.read', OverrideType::Deny);

    $result = $resolver->resolve([], [$userPermissionOverride, $denyOverride], modules());

    $usersRead = findPermission($result, 'users.list.read');

    assert($usersRead instanceof EffectivePermission);

    expect($usersRead->granted)->toBeFalse()
        ->and($usersRead->source)->toBe('user:deny');
});

it('deny at module level overrides grant at action level', function (): void {
    $resolver = new PermissionResolver;

    $userPermissionOverride = makeOverride('users.list.read', OverrideType::Grant);
    $denyOverride = makeOverride('users', OverrideType::Deny);

    $result = $resolver->resolve([], [$userPermissionOverride, $denyOverride], modules());

    $usersRead = findPermission($result, 'users.list.read');
    $usersCreate = findPermission($result, 'users.list.create');

    assert($usersRead instanceof EffectivePermission);
    assert($usersCreate instanceof EffectivePermission);

    expect($usersRead->granted)->toBeFalse()
        ->and($usersCreate->granted)->toBeFalse();
});

it('grant override replaces role grant with different scope', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Editor', ['users.list.read' => 'team']);
    $userPermissionOverride = makeOverride('users.list.read', OverrideType::Grant, AccessScope::All);

    $result = $resolver->resolve([$role], [$userPermissionOverride], modules());

    $usersRead = findPermission($result, 'users.list.read');

    assert($usersRead instanceof EffectivePermission);

    expect($usersRead->granted)->toBeTrue()
        ->and($usersRead->scope)->toBe(AccessScope::All)
        ->and($usersRead->source)->toBe('user:grant');
});

it('scope hierarchy is all > team > own', function (): void {
    expect(AccessScope::All->isMorePermissiveThan(AccessScope::Team))->toBeTrue()
        ->and(AccessScope::Team->isMorePermissiveThan(AccessScope::Own))->toBeTrue()
        ->and(AccessScope::Own->isMorePermissiveThan(AccessScope::All))->toBeFalse()
        ->and(AccessScope::Team->isMorePermissiveThan(AccessScope::All))->toBeFalse();
});

it('returns empty for modules not in available modules', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Editor', ['unknown' => 'all']);

    $result = $resolver->resolve([$role], [], modules());

    foreach ($result as $permission) {
        expect($permission->permissionKey->module->value)->not->toBe('unknown');
    }
});

it('records source as role name for role-granted permissions', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Editor', ['users.list.read' => 'all']);

    $result = $resolver->resolve([$role], [], modules());

    $usersRead = findPermission($result, 'users.list.read');

    assert($usersRead instanceof EffectivePermission);

    expect($usersRead->source)->toBe('role:Editor');
});

it('handles feature-level deny override expanding to all actions', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Admin', ['crm' => 'all']);
    $userPermissionOverride = makeOverride('crm.deals', OverrideType::Deny);

    $result = $resolver->resolve([$role], [$userPermissionOverride], modules());

    $contactsRead = findPermission($result, 'crm.contacts.read');
    $dealsRead = findPermission($result, 'crm.deals.read');
    $dealsCreate = findPermission($result, 'crm.deals.create');

    assert($contactsRead instanceof EffectivePermission);
    assert($dealsRead instanceof EffectivePermission);
    assert($dealsCreate instanceof EffectivePermission);

    expect($contactsRead->granted)->toBeTrue()
        ->and($dealsRead->granted)->toBeFalse()
        ->and($dealsCreate->granted)->toBeFalse();
});

it('existing deny override skips subsequent grant override', function (): void {
    $resolver = new PermissionResolver;

    $userPermissionOverride = makeOverride('users.list.read', OverrideType::Deny);
    $grantOverride = makeOverride('users.list.read', OverrideType::Grant);

    $result = $resolver->resolve([], [$userPermissionOverride, $grantOverride], modules());

    $usersRead = findPermission($result, 'users.list.read');

    assert($usersRead instanceof EffectivePermission);

    expect($usersRead->granted)->toBeFalse()
        ->and($usersRead->source)->toBe('user:deny');
});

it('empty available modules returns empty result', function (): void {
    $resolver = new PermissionResolver;

    $role = makeRole('Admin', ['users' => 'all']);

    $result = $resolver->resolve([$role], [], []);

    expect($result)->toHaveCount(0);
});
