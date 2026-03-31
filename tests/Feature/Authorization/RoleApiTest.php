<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

function apiUser(): UserModel
{
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440900', 'name' => 'API Admin', 'email' => 'apiadmin@test.com']);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    return $user;
}

it('lists roles via API', function (): void {
    apiUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440901', 'name' => 'Editor', 'description' => 'Ed', 'is_system' => false]);

    $this->getJson('/api/v1/roles')->assertOk()->assertJsonCount(2, 'data');
});

it('creates a role via API', function (): void {
    apiUser();

    $this->postJson('/api/v1/roles', [
        'name' => 'New Role',
        'description' => 'A new role',
        'permissions' => [['permission' => 'users.list.read', 'scope' => 'all']],
    ])->assertCreated()->assertJsonStructure(['id']);

    $this->assertDatabaseHas('roles', ['name' => 'New Role']);
});

it('gets a role by id via API', function (): void {
    apiUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440902', 'name' => 'Viewer', 'description' => 'V', 'is_system' => false]);

    $this->getJson('/api/v1/roles/550e8400-e29b-41d4-a716-446655440902')
        ->assertOk()
        ->assertJsonFragment(['name' => 'Viewer']);
});

it('updates a role via API', function (): void {
    apiUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440903', 'name' => 'Old', 'description' => 'Old', 'is_system' => false]);

    $this->putJson('/api/v1/roles/550e8400-e29b-41d4-a716-446655440903', [
        'name' => 'Updated',
        'description' => 'Updated desc',
        'permissions' => [['permission' => 'users.list.read', 'scope' => 'all']],
    ])->assertOk();

    $this->assertDatabaseHas('roles', ['name' => 'Updated']);
});

it('deletes a role via API', function (): void {
    apiUser();
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-446655440904', 'name' => 'ToDelete', 'description' => 'D', 'is_system' => false]);

    $this->deleteJson('/api/v1/roles/550e8400-e29b-41d4-a716-446655440904')->assertNoContent();
});

it('validates create role request', function (): void {
    apiUser();

    $this->postJson('/api/v1/roles', [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'description', 'permissions']);
});

it('returns 401 when unauthenticated', function (): void {
    $this->getJson('/api/v1/roles')->assertUnauthorized();
});

it('returns 403 when user has no permissions', function (): void {
    $user = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440905', 'name' => 'No Perms', 'email' => 'noperms@test.com']);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/roles')->assertForbidden();
});
