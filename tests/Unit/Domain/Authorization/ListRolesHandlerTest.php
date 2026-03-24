<?php

declare(strict_types=1);

use App\Domain\Authorization\Query\ListRoles\ListRolesHandler;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeRoleRepository;

it('returns all roles', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        '00000000-0000-0000-0000-000000000001',
        new RoleName('Editor'),
        'Editor role',
        false,
        [],
    );

    $roleRepo = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);

    $handler = new ListRolesHandler($roleRepo);

    $result = $handler->handle(new ListRolesQuery);

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Editor');
});

it('returns empty list when no roles exist', function (): void {
    $roleRepo = new FakeRoleRepository;

    $handler = new ListRolesHandler($roleRepo);

    $result = $handler->handle(new ListRolesQuery);

    expect($result)->toHaveCount(0);
});
