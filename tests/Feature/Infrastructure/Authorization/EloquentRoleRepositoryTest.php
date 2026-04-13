<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RoleName;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;
use App\Domain\Authorization\ValueObject\Module;
use App\Domain\Authorization\ValueObject\PermissionKey;
use App\Infrastructure\Eloquent\Authorization\EloquentRoleRepository;
use App\Infrastructure\Eloquent\Authorization\RoleMapper;

function roleRepo(): EloquentRoleRepository
{
    return new EloquentRoleRepository(new RoleMapper);
}

function makeTestRole(string $id, string $name, bool $isSystem = false): Role
{
    return new Role(
        id: new RoleId($id),
        name: new RoleName($name),
        description: $name.' desc',
        isSystem: $isSystem,
        permissions: [
            new RolePermission(new PermissionKey(new Module('users')), AccessScope::All),
        ],
    );
}

it('creates and finds a role by id', function (): void {
    $eloquentRoleRepository = roleRepo();
    $role = makeTestRole('550e8400-e29b-41d4-a716-446655440800', 'Editor');

    $eloquentRoleRepository->create($role);
    $found = $eloquentRoleRepository->findById($role->id);

    expect($found)->not->toBeNull();
    expect($found->name->value)->toBe('Editor');
    expect($found->permissions)->toHaveCount(1);
});

it('finds all roles', function (): void {
    $eloquentRoleRepository = roleRepo();
    $eloquentRoleRepository->create(makeTestRole('550e8400-e29b-41d4-a716-446655440801', 'Admin'));
    $eloquentRoleRepository->create(makeTestRole('550e8400-e29b-41d4-a716-446655440802', 'Other'));

    $roles = $eloquentRoleRepository->findAll();

    expect($roles)->toHaveCount(2);
});

it('finds by name', function (): void {
    $eloquentRoleRepository = roleRepo();
    $eloquentRoleRepository->create(makeTestRole('550e8400-e29b-41d4-a716-446655440803', 'Editor'));

    $found = $eloquentRoleRepository->findByName('Editor');
    $notFound = $eloquentRoleRepository->findByName('Missing');

    expect($found)->not->toBeNull();
    expect($notFound)->toBeNull();
});

it('finds system roles', function (): void {
    $eloquentRoleRepository = roleRepo();
    $eloquentRoleRepository->create(new Role(
        id: new RoleId('550e8400-e29b-41d4-a716-446655440804'),
        name: new RoleName('Super Admin'),
        description: 'System',
        isSystem: true,
        permissions: [],
    ));
    $eloquentRoleRepository->create(makeTestRole('550e8400-e29b-41d4-a716-446655440805', 'Regular'));

    $system = $eloquentRoleRepository->findSystemRoles();

    expect($system)->toHaveCount(1);
    expect($system[0]->isSystem)->toBeTrue();
});

it('updates a role', function (): void {
    $eloquentRoleRepository = roleRepo();
    $eloquentRoleRepository->create(makeTestRole('550e8400-e29b-41d4-a716-446655440806', 'Old Name'));

    $updated = new Role(
        id: new RoleId('550e8400-e29b-41d4-a716-446655440806'),
        name: new RoleName('New Name'),
        description: 'Updated',
        isSystem: false,
        permissions: [],
    );
    $eloquentRoleRepository->update($updated);

    $found = $eloquentRoleRepository->findById(new RoleId('550e8400-e29b-41d4-a716-446655440806'));
    expect($found->name->value)->toBe('New Name');
    expect($found->permissions)->toHaveCount(0);
});

it('deletes a role', function (): void {
    $eloquentRoleRepository = roleRepo();
    $eloquentRoleRepository->create(makeTestRole('550e8400-e29b-41d4-a716-446655440807', 'ToDelete'));

    $eloquentRoleRepository->delete(new RoleId('550e8400-e29b-41d4-a716-446655440807'));

    expect($eloquentRoleRepository->findById(new RoleId('550e8400-e29b-41d4-a716-446655440807')))->toBeNull();
});

it('returns null for non-existent role', function (): void {
    expect(roleRepo()->findById(new RoleId('550e8400-e29b-41d4-a716-446655440899')))->toBeNull();
});

it('delete is no-op for non-existent role', function (): void {
    roleRepo()->delete(new RoleId('550e8400-e29b-41d4-a716-446655440899'));
    expect(true)->toBeTrue();
});

it('counts roles', function (): void {
    $eloquentRoleRepository = roleRepo();
    $eloquentRoleRepository->create(makeTestRole('550e8400-e29b-41d4-a716-446655440810', 'CountA'));
    $eloquentRoleRepository->create(makeTestRole('550e8400-e29b-41d4-a716-446655440811', 'CountB'));

    expect($eloquentRoleRepository->count())->toBe(2);
});

it('returns zero count when no roles exist', function (): void {
    expect(roleRepo()->count())->toBe(0);
});
