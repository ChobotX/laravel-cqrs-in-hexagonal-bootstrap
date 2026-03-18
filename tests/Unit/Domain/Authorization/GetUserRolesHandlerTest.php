<?php

declare(strict_types=1);

use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesHandler;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeUserPermissionRepository;

it('returns roles assigned to a user in an organization', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        '00000000-0000-0000-0000-000000000001',
        new RoleName('Editor'),
        'Editor role',
        false,
        [],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['00000000-0000-0000-0000-000000000010:00000000-0000-0000-0000-000000000001'] = [$role];

    $handler = new GetUserRolesHandler($userPermRepo);

    $result = $handler->handle(new GetUserRolesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Editor');
});

it('returns empty list when user has no roles', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;

    $handler = new GetUserRolesHandler($userPermRepo);

    $result = $handler->handle(new GetUserRolesQuery(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '00000000-0000-0000-0000-000000000001',
    ));

    expect($result)->toHaveCount(0);
});
