<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Command\SeedDefaultRoles\SeedDefaultRolesHandler;
use App\Domain\Authorization\Contract\Command\SeedDefaultRoles\SeedDefaultRolesCommand;
use App\Domain\Authorization\Contract\Role;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeRoleRepository;

/** @return array<string, array{features: array<string, array{actions: list<string>}>}> */
function testModules(): array
{
    return [
        'users' => [
            'features' => [
                'list' => ['actions' => ['read', 'create', 'update', 'delete']],
            ],
        ],
    ];
}

function seedRoles(): FakeRoleRepository
{
    $repository = new FakeRoleRepository;

    $handler = new SeedDefaultRolesHandler(
        $repository,
        new FakeIdGenerator,
        testModules(),
    );

    $handler->handle(new SeedDefaultRolesCommand);

    return $repository;
}

function findRole(FakeRoleRepository $fakeRoleRepository, string $name): Role
{
    foreach ($fakeRoleRepository->saved as $role) {
        if ($role->name->value === $name) {
            return $role;
        }
    }

    throw new RuntimeException(sprintf("Role '%s' not found", $name));
}

it('seeds four default roles', function (): void {
    $fakeRoleRepository = seedRoles();

    expect($fakeRoleRepository->saved)->toHaveCount(4);

    $names = array_map(fn (Role $role): string => $role->name->value, $fakeRoleRepository->saved);
    expect($names)->toContain('Manager', 'Team Leader', 'Team Member', 'Externist');
});

it('manager role has all module permissions with all scope', function (): void {
    $role = findRole(seedRoles(), 'Manager');

    expect($role->permissions)->toHaveCount(1)
        ->and((string) $role->permissions[0]->permissionKey)->toBe('users')
        ->and($role->permissions[0]->scope)->toBe(AccessScope::All);
});

it('team member role has read at all scope and create update at own scope', function (): void {
    $role = findRole(seedRoles(), 'Team Member');

    $byKey = [];

    foreach ($role->permissions as $permission) {
        $byKey[(string) $permission->permissionKey] = $permission->scope;
    }

    expect($role->permissions)->toHaveCount(3)
        ->and($byKey['users.list.read'])->toBe(AccessScope::All)
        ->and($byKey['users.list.create'])->toBe(AccessScope::Own)
        ->and($byKey['users.list.update'])->toBe(AccessScope::Own);
});

it('externist role has read create update at own scope', function (): void {
    $role = findRole(seedRoles(), 'Externist');

    $byKey = [];

    foreach ($role->permissions as $permission) {
        $byKey[(string) $permission->permissionKey] = $permission->scope;
    }

    expect($role->permissions)->toHaveCount(3)
        ->and($byKey['users.list.read'])->toBe(AccessScope::Own)
        ->and($byKey['users.list.create'])->toBe(AccessScope::Own)
        ->and($byKey['users.list.update'])->toBe(AccessScope::Own);
});
