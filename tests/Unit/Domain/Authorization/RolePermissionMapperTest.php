<?php

declare(strict_types=1);

use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\RolePermissionMapper;

it('maps full permission data with module, feature and action', function (): void {
    $mapper = new RolePermissionMapper;

    $rolePermission = $mapper->map([
        'permission' => 'users.list.read',
        'scope' => 'all',
    ]);

    expect($rolePermission->permissionKey->module->value)->toBe('users');
    expect($rolePermission->permissionKey->feature?->value)->toBe('list');
    expect($rolePermission->permissionKey->action)->toBe(Action::Read);
    expect($rolePermission->scope)->toBe(AccessScope::All);
});

it('maps permission data with module only', function (): void {
    $mapper = new RolePermissionMapper;

    $rolePermission = $mapper->map([
        'permission' => 'users',
        'scope' => 'team',
    ]);

    expect($rolePermission->permissionKey->module->value)->toBe('users');
    expect($rolePermission->permissionKey->feature)->toBeNull();
    expect($rolePermission->permissionKey->action)->toBeNull();
    expect($rolePermission->scope)->toBe(AccessScope::Team);
});

it('maps permission data with module and feature', function (): void {
    $mapper = new RolePermissionMapper;

    $rolePermission = $mapper->map([
        'permission' => 'users.list',
        'scope' => 'own',
    ]);

    expect($rolePermission->permissionKey->module->value)->toBe('users');
    expect($rolePermission->permissionKey->feature?->value)->toBe('list');
    expect($rolePermission->permissionKey->action)->toBeNull();
    expect($rolePermission->scope)->toBe(AccessScope::Own);
});
