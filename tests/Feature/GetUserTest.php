<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

it('returns a user by id', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440200',
        'name' => 'Auth User',
        'email' => 'auth@example.com',
    ]);
    $this->assignSuperAdmin($authUser->id);
    Sanctum::actingAs($authUser);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440000',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response = $this->getJson('/api/users/'.$user->id);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
        ]);
});

it('returns 404 when user not found', function (): void {
    $this->seedSuperAdminRole();
    $authUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440201',
        'name' => 'Auth User',
        'email' => 'auth@example.com',
    ]);
    $this->assignSuperAdmin($authUser->id);
    Sanctum::actingAs($authUser);

    $response = $this->getJson('/api/users/550e8400-e29b-41d4-a716-446655440000');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'User with id [550e8400-e29b-41d4-a716-446655440000] not found.',
        ]);
});

it('returns 401 when unauthenticated', function (): void {
    $this->getJson('/api/users/550e8400-e29b-41d4-a716-446655440000')
        ->assertUnauthorized();
});
