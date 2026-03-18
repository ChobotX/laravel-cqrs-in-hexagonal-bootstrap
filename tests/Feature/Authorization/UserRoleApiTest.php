<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

it('assigns a role to a user via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440920', 'name' => 'Admin', 'email' => 'admrole@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $target = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440921', 'name' => 'Target', 'email' => 'target@test.com']);
    $role = RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440922', 'name' => 'Editor', 'description' => 'Ed', 'is_system' => false, 'organization_id' => '00000000-0000-0000-0000-000000000001']);

    $this->postJson(sprintf('/api/users/%s/roles', $target->id), ['role_id' => $role->id])->assertCreated();

    $this->assertDatabaseHas('user_roles', ['user_id' => $target->id, 'role_id' => $role->id]);
});

it('lists user roles via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440923', 'name' => 'Admin', 'email' => 'admlist@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->getJson(sprintf('/api/users/%s/roles', $admin->id))->assertOk();
});

it('revokes a role from user via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440924', 'name' => 'Admin', 'email' => 'admrev@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $target = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440925', 'name' => 'Target', 'email' => 'trev@test.com']);
    $role = RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440926', 'name' => 'Ed', 'description' => 'Ed', 'is_system' => false, 'organization_id' => '00000000-0000-0000-0000-000000000001']);
    $this->assignRole($target->id, $role->id, '00000000-0000-0000-0000-000000000001');

    $this->deleteJson(sprintf('/api/users/%s/roles/%s', $target->id, $role->id))->assertNoContent();
});

it('returns 403 on list user roles without org', function (): void {
    config(['authorization.default_organization_id' => null]);
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440927', 'name' => 'A', 'email' => 'anullorg@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->getJson(sprintf('/api/users/%s/roles', $admin->id))->assertForbidden();
});

it('returns 403 on assign role without org', function (): void {
    config(['authorization.default_organization_id' => null]);
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440928', 'name' => 'A', 'email' => 'anullorg2@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->postJson(sprintf('/api/users/%s/roles', $admin->id), ['role_id' => '550e8400-e29b-41d4-a716-446655440922'])->assertForbidden();
});

it('returns 403 on revoke role without org', function (): void {
    config(['authorization.default_organization_id' => null]);
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440929', 'name' => 'A', 'email' => 'anullorg3@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->deleteJson(sprintf('/api/users/%s/roles/%s', $admin->id, '550e8400-e29b-41d4-a716-446655440922'))->assertForbidden();
});
