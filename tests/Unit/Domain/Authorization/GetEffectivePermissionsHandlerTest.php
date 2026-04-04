<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Contract\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Contract\RolePermission;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsHandler;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeUserPermissionRepository;

it('resolves effective permissions for a user', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Editor role',
        false,
        [new RolePermission(new PermissionKey(new Module('users')), AccessScope::All)],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['00000000-0000-0000-0000-000000000010'] = [$role];

    $availableModules = [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create']],
            ],
        ],
    ];

    $handler = new GetEffectivePermissionsHandler(
        $userPermRepo,
        new PermissionResolver,
        $availableModules,
    );

    $result = $handler->handle(new GetEffectivePermissionsQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(2);

    foreach ($result as $permission) {
        expect($permission->granted)->toBeTrue()
            ->and($permission->scope)->toBe(AccessScope::All);
    }
});

it('returns empty permissions when no available modules', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;

    $handler = new GetEffectivePermissionsHandler(
        $userPermRepo,
        new PermissionResolver,
        [],
    );

    $result = $handler->handle(new GetEffectivePermissionsQuery(
        userId: '00000000-0000-0000-0000-000000000010',
    ));

    expect($result)->toHaveCount(0);
});
