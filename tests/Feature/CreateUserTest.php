<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

it('creates a user and returns 201 with id', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440100',
        'name' => 'Auth User',
        'email' => 'auth@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['id']);

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('returns 422 for invalid data', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440101',
        'name' => 'Auth User',
        'email' => 'auth@example.com',
    ]);
    $this->assignSuperAdmin($user->id);
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/users', [
        'name' => '',
        'email' => 'not-an-email',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email']);
});

it('returns 401 when unauthenticated', function (): void {
    $this->postJson('/api/v1/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])->assertUnauthorized();
});
