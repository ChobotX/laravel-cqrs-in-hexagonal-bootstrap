<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Query\GetRoleByIdQuery;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RoleName;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Handler\Query\GetRoleByIdHandler;
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
