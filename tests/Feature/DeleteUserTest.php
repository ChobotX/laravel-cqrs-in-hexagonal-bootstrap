<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

it('soft deletes a user and returns 204', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440400',
        'name' => 'Auth User',
        'email' => 'auth@example.com',
    ]);
    $this->assignSuperAdmin($authUser->id);
    Sanctum::actingAs($authUser);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response = $this->deleteJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000');

    $response->assertStatus(204);

    $this->assertSoftDeleted('users', [
        'id' => '550e8400-e29b-41d4-a716-446655440000',
    ]);
});

it('returns 404 when user not found', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440401',
        'name' => 'Auth User',
        'email' => 'auth@example.com',
    ]);
    $this->assignSuperAdmin($authUser->id);
    Sanctum::actingAs($authUser);

    $response = $this->deleteJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000');

    $response->assertStatus(404);
});

it('returns 404 on GET after delete', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440402',
        'name' => 'Auth User',
        'email' => 'auth@example.com',
    ]);
    $this->assignSuperAdmin($authUser->id);
    Sanctum::actingAs($authUser);

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->deleteJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000')
        ->assertStatus(204);

    $this->getJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000')
        ->assertStatus(404);
});

it('returns 401 when unauthenticated', function (): void {
    $this->deleteJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000')
        ->assertUnauthorized();
});
