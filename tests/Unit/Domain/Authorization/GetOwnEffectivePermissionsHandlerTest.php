<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Contract\RolePermission;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\Query\GetOwnEffectivePermissions\GetOwnEffectivePermissionsHandler;
use App\Domain\Authorization\Query\GetOwnEffectivePermissions\GetOwnEffectivePermissionsQuery;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeUserPermissionRepository;

it('resolves own effective permissions', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Editor role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::All)],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['00000000-0000-0000-0000-000000000010'] = [$role];

    $handler = new GetOwnEffectivePermissionsHandler(
        $userPermRepo,
        new PermissionResolver,
        ['users' => ['features' => ['list' => ['actions' => ['read', 'create']]]]],
    );

    $result = $handler->handle(new GetOwnEffectivePermissionsQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(2);

    foreach ($result as $permission) {
        expect($permission->granted)->toBeTrue()
            ->and($permission->scope)->toBe(AccessScope::All);
    }
});

it('returns empty permissions when no available modules', function (): void {
    $handler = new GetOwnEffectivePermissionsHandler(
        new FakeUserPermissionRepository,
        new PermissionResolver,
        [],
    );

    $result = $handler->handle(new GetOwnEffectivePermissionsQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(0);
});
