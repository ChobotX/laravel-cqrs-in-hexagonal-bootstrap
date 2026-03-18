<?php

declare(strict_types=1);

use App\Domain\Authorization\AccessScope;
use App\Infrastructure\Eloquent\Authorization\RoleMapper;
use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\RolePermissionModel;
use Illuminate\Database\Eloquent\Collection;

it('maps a role model to domain with permissions', function (): void {
    $roleModel = new RoleModel;
    $roleModel->id = '550e8400-e29b-41d4-a716-446655440001';
    $roleModel->organization_id = 'org-1';
    $roleModel->name = 'Editor';
    $roleModel->description = 'Can edit';
    $roleModel->is_system = false;

    $permissionModel = new RolePermissionModel;
    $permissionModel->module = 'users';
    $permissionModel->feature = 'list';
    $permissionModel->action = 'read';
    $permissionModel->scope = 'all';

    $roleModel->setRelation('permissions', new Collection([$permissionModel]));

    $mapper = new RoleMapper;
    $role = $mapper->toDomain($roleModel);

    expect($role->id->value)->toBe('550e8400-e29b-41d4-a716-446655440001')
        ->and($role->organizationId)->toBe('org-1')
        ->and($role->name->value)->toBe('Editor')
        ->and($role->description)->toBe('Can edit')
        ->and($role->isSystem)->toBeFalse()
        ->and($role->permissions)->toHaveCount(1)
        ->and((string) $role->permissions[0]->permissionKey)->toBe('users.list.read')
        ->and($role->permissions[0]->scope)->toBe(AccessScope::All);
});

it('maps a role model with no loaded permissions to empty array', function (): void {
    $roleModel = new RoleModel;
    $roleModel->id = '550e8400-e29b-41d4-a716-446655440002';
    $roleModel->organization_id = 'org-1';
    $roleModel->name = 'Viewer';
    $roleModel->description = 'Can view';
    $roleModel->is_system = false;

    $mapper = new RoleMapper;
    $role = $mapper->toDomain($roleModel);

    expect($role->permissions)->toBe([]);
});

it('maps permission with null feature and null action', function (): void {
    $roleModel = new RoleModel;
    $roleModel->id = '550e8400-e29b-41d4-a716-446655440003';
    $roleModel->organization_id = 'org-1';
    $roleModel->name = 'Module Admin';
    $roleModel->description = 'Full module access';
    $roleModel->is_system = false;

    $permissionModel = new RolePermissionModel;
    $permissionModel->module = 'users';
    $permissionModel->feature = null;
    $permissionModel->action = null;
    $permissionModel->scope = 'all';

    $roleModel->setRelation('permissions', new Collection([$permissionModel]));

    $mapper = new RoleMapper;
    $role = $mapper->toDomain($roleModel);

    expect($role->permissions)->toHaveCount(1)
        ->and((string) $role->permissions[0]->permissionKey)->toBe('users')
        ->and($role->permissions[0]->permissionKey->feature)->toBeNull()
        ->and($role->permissions[0]->permissionKey->action)->toBeNull();
});

it('maps permission with feature but null action', function (): void {
    $roleModel = new RoleModel;
    $roleModel->id = '550e8400-e29b-41d4-a716-446655440004';
    $roleModel->organization_id = 'org-1';
    $roleModel->name = 'Feature Admin';
    $roleModel->description = 'Full feature access';
    $roleModel->is_system = false;

    $permissionModel = new RolePermissionModel;
    $permissionModel->module = 'users';
    $permissionModel->feature = 'list';
    $permissionModel->action = null;
    $permissionModel->scope = 'team';

    $roleModel->setRelation('permissions', new Collection([$permissionModel]));

    $mapper = new RoleMapper;
    $role = $mapper->toDomain($roleModel);

    expect($role->permissions)->toHaveCount(1)
        ->and((string) $role->permissions[0]->permissionKey)->toBe('users.list')
        ->and($role->permissions[0]->permissionKey->action)->toBeNull()
        ->and($role->permissions[0]->scope)->toBe(AccessScope::Team);
});

it('maps system role', function (): void {
    $roleModel = new RoleModel;
    $roleModel->id = '550e8400-e29b-41d4-a716-446655440005';
    $roleModel->organization_id = null;
    $roleModel->name = 'Super Admin';
    $roleModel->description = 'System super admin';
    $roleModel->is_system = true;
    $roleModel->setRelation('permissions', new Collection([]));

    $mapper = new RoleMapper;
    $role = $mapper->toDomain($roleModel);

    expect($role->isSystem)->toBeTrue()
        ->and($role->organizationId)->toBeNull();
});
