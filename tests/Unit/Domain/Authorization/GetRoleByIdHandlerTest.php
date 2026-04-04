<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Query\GetRoleById\GetRoleByIdHandler;
use App\Domain\Authorization\Query\GetRoleById\GetRoleByIdQuery;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeRoleRepository;

it('returns a role when found', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Editor role',
        false,
        [],
    );

    $roleRepo = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);

    $handler = new GetRoleByIdHandler($roleRepo);

    $result = $handler->handle(new GetRoleByIdQuery(
        id: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($result->name->value)->toBe('Editor');
});

it('throws when role does not exist', function (): void {
    $roleRepo = new FakeRoleRepository;

    $handler = new GetRoleByIdHandler($roleRepo);

    $handler->handle(new GetRoleByIdQuery(
        id: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(RoleNotFoundException::class);
