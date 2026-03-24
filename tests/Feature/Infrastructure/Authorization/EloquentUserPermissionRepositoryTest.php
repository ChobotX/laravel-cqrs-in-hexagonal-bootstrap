<?php

declare(strict_types=1);

use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\RoleId;
use App\Infrastructure\Eloquent\Authorization\EloquentUserPermissionRepository;
use App\Infrastructure\Eloquent\Authorization\RoleMapper;
use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\User\UserModel;

function userPermRepo(): EloquentUserPermissionRepository
{
    return new EloquentUserPermissionRepository(new RoleMapper);
}

function createTestUser(string $id): UserModel
{
    return UserModel::create(['id' => $id, 'name' => 'Test', 'email' => $id.'@test.com']);
}

function createTestRoleModel(string $id, string $name): RoleModel
{
    return RoleModel::create([
        'id' => $id,
        'name' => $name,
        'description' => $name,
        'is_system' => false,
    ]);
}

it('assigns and retrieves user roles', function (): void {
    $eloquentUserPermissionRepository = userPermRepo();
    $userModel = createTestUser('550e8400-e29b-41d4-a716-446655440700');
    $roleModel = createTestRoleModel('550e8400-e29b-41d4-a716-446655440701', 'Editor');

    $eloquentUserPermissionRepository->assignRole($userModel->id, new RoleId($roleModel->id));
    $roles = $eloquentUserPermissionRepository->userRoles($userModel->id);

    expect($roles)->toHaveCount(1);
    expect($roles[0]->name->value)->toBe('Editor');
});

it('checks hasRole correctly', function (): void {
    $eloquentUserPermissionRepository = userPermRepo();
    $userModel = createTestUser('550e8400-e29b-41d4-a716-446655440702');
    $roleModel = createTestRoleModel('550e8400-e29b-41d4-a716-446655440703', 'Admin');

    expect($eloquentUserPermissionRepository->hasRole($userModel->id, new RoleId($roleModel->id)))->toBeFalse();

    $eloquentUserPermissionRepository->assignRole($userModel->id, new RoleId($roleModel->id));

    expect($eloquentUserPermissionRepository->hasRole($userModel->id, new RoleId($roleModel->id)))->toBeTrue();
});

it('revokes a role', function (): void {
    $eloquentUserPermissionRepository = userPermRepo();
    $userModel = createTestUser('550e8400-e29b-41d4-a716-446655440704');
    $roleModel = createTestRoleModel('550e8400-e29b-41d4-a716-446655440705', 'Viewer');

    $eloquentUserPermissionRepository->assignRole($userModel->id, new RoleId($roleModel->id));
    $eloquentUserPermissionRepository->revokeRole($userModel->id, new RoleId($roleModel->id));

    expect($eloquentUserPermissionRepository->userRoles($userModel->id))->toHaveCount(0);
});

it('sets and retrieves overrides', function (): void {
    $eloquentUserPermissionRepository = userPermRepo();
    $userModel = createTestUser('550e8400-e29b-41d4-a716-446655440706');
    $key = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);

    $eloquentUserPermissionRepository->setOverride($userModel->id, $key, OverrideType::Deny, AccessScope::All);

    $overrides = $eloquentUserPermissionRepository->userOverrides($userModel->id);

    expect($overrides)->toHaveCount(1);
    expect($overrides[0]->type)->toBe(OverrideType::Deny);
});

it('removes an override', function (): void {
    $eloquentUserPermissionRepository = userPermRepo();
    $userModel = createTestUser('550e8400-e29b-41d4-a716-446655440707');
    $key = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);

    $eloquentUserPermissionRepository->setOverride($userModel->id, $key, OverrideType::Grant, AccessScope::All);
    $eloquentUserPermissionRepository->removeOverride($userModel->id, $key);

    expect($eloquentUserPermissionRepository->userOverrides($userModel->id))->toHaveCount(0);
});

it('upserts override on duplicate', function (): void {
    $eloquentUserPermissionRepository = userPermRepo();
    $userModel = createTestUser('550e8400-e29b-41d4-a716-446655440708');
    $key = new PermissionKey(new Module('users'), new Feature('list'), Action::Read);

    $eloquentUserPermissionRepository->setOverride($userModel->id, $key, OverrideType::Grant, AccessScope::Own);
    $eloquentUserPermissionRepository->setOverride($userModel->id, $key, OverrideType::Deny, AccessScope::All);

    $overrides = $eloquentUserPermissionRepository->userOverrides($userModel->id);

    expect($overrides)->toHaveCount(1);
    expect($overrides[0]->type)->toBe(OverrideType::Deny);
});

it('finds user ids with role', function (): void {
    $eloquentUserPermissionRepository = userPermRepo();
    $userModel = createTestUser('550e8400-e29b-41d4-a716-446655440709');
    $user2 = createTestUser('550e8400-e29b-41d4-a716-446655440710');
    $roleModel = createTestRoleModel('550e8400-e29b-41d4-a716-446655440711', 'Shared');

    $eloquentUserPermissionRepository->assignRole($userModel->id, new RoleId($roleModel->id));
    $eloquentUserPermissionRepository->assignRole($user2->id, new RoleId($roleModel->id));

    $userIds = $eloquentUserPermissionRepository->userIdsWithRole(new RoleId($roleModel->id));

    expect($userIds)->toHaveCount(2);
    expect($userIds)->toContain($userModel->id);
    expect($userIds)->toContain($user2->id);
});
