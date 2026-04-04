<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Query\CountRolesQuery;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;
use App\Domain\Authorization\Handler\Query\CountRolesHandler;
use App\Domain\Authorization\ValueObject\Module;
use App\Domain\Authorization\ValueObject\PermissionKey;
use App\Domain\Authorization\ValueObject\RoleName;
use Tests\Helper\FakeRoleRepository;

it('returns the role count from the repository', function (): void {
    $roles = [
        '550e8400-e29b-41d4-a716-446655440000' => new Role(
            new RoleId('550e8400-e29b-41d4-a716-446655440000'),
            new RoleName('Admin'),
            'Admin role',
            false,
            [new RolePermission(new PermissionKey(new Module('users')), AccessScope::All)],
        ),
    ];

    $handler = new CountRolesHandler(new FakeRoleRepository($roles));

    expect($handler->handle(new CountRolesQuery))->toBe(1);
});

it('returns zero when no roles exist', function (): void {
    $handler = new CountRolesHandler(new FakeRoleRepository);

    expect($handler->handle(new CountRolesQuery))->toBe(0);
});
