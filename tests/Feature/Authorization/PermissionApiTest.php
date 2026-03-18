<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

it('gets user permissions via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440930', 'name' => 'Admin', 'email' => 'perma@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->getJson(sprintf('/api/users/%s/permissions', $admin->id))->assertOk();
});

it('sets a permission override via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440931', 'name' => 'Admin', 'email' => 'permb@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $target = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440932', 'name' => 'Target', 'email' => 'permc@test.com']);

    $this->putJson(sprintf('/api/users/%s/permissions', $target->id), [
        'permission' => 'users.list.read',
        'type' => 'deny',
        'scope' => 'all',
    ])->assertOk();

    $this->assertDatabaseHas('user_permission_overrides', [
        'user_id' => $target->id,
        'module' => 'users',
        'feature' => 'list',
        'action' => 'read',
        'type' => 'deny',
    ]);
});

it('removes a permission override via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440933', 'name' => 'Admin', 'email' => 'permd@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $target = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440934', 'name' => 'Target', 'email' => 'perme@test.com']);

    $this->putJson(sprintf('/api/users/%s/permissions', $target->id), [
        'permission' => 'users.list.read',
        'type' => 'grant',
        'scope' => 'all',
    ]);

    $this->deleteJson(sprintf('/api/users/%s/permissions/users.list.read', $target->id))->assertNoContent();
});

it('gets effective permissions via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440935', 'name' => 'Admin', 'email' => 'permf@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->getJson(sprintf('/api/users/%s/effective-permissions', $admin->id))->assertOk()->assertJsonStructure(['data']);
});

it('returns 403 on get permissions without org', function (): void {
    config(['authorization.default_organization_id' => null]);
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440936', 'name' => 'A', 'email' => 'permno@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->getJson(sprintf('/api/users/%s/permissions', $admin->id))->assertForbidden();
});

it('returns 403 on effective permissions without org', function (): void {
    config(['authorization.default_organization_id' => null]);
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440937', 'name' => 'A', 'email' => 'effno@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->getJson(sprintf('/api/users/%s/effective-permissions', $admin->id))->assertForbidden();
});

it('returns 403 on set override without org', function (): void {
    config(['authorization.default_organization_id' => null]);
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440938', 'name' => 'A', 'email' => 'setno@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->putJson(sprintf('/api/users/%s/permissions', $admin->id), [
        'permission' => 'users.list.read', 'type' => 'deny', 'scope' => 'all',
    ])->assertForbidden();
});

it('returns 403 on remove override without org', function (): void {
    config(['authorization.default_organization_id' => null]);
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440939', 'name' => 'A', 'email' => 'rmno@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->deleteJson(sprintf('/api/users/%s/permissions/users.list.read', $admin->id))->assertForbidden();
});
