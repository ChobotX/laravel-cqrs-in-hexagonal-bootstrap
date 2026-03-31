<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

it('updates a user and returns 204', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440300',
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

    $response = $this->putJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $response->assertStatus(204);

    $this->assertDatabaseHas('users', [
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

it('returns 404 when user not found', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440301',
        'name' => 'Auth User',
        'email' => 'auth@example.com',
    ]);
    $this->assignSuperAdmin($authUser->id);
    Sanctum::actingAs($authUser);

    $response = $this->putJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $response->assertStatus(404);
});

it('returns 422 for invalid data', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440302',
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

    $response = $this->putJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000', [
        'name' => '',
        'email' => 'not-an-email',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

it('allows keeping the same email', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440303',
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

    $response = $this->putJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000', [
        'name' => 'John Updated',
        'email' => 'john@example.com',
    ]);

    $response->assertStatus(204);
});

it('rejects a duplicate email from another user', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440304',
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

    UserModel::create([
        'id' => '660e8400-e29b-41d4-a716-446655440000',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $response = $this->putJson('/api/v1/users/660e8400-e29b-41d4-a716-446655440000', [
        'name' => 'Jane Doe',
        'email' => 'john@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('returns 401 when unauthenticated', function (): void {
    $this->putJson('/api/v1/users/550e8400-e29b-41d4-a716-446655440000', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ])->assertUnauthorized();
});
