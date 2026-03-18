<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

it('returns list of users as JSON', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440850',
        'name' => 'API User',
        'email' => 'api-list@example.com',
    ]);
    $this->assignSuperAdmin($user->id);

    UserModel::create([
        'id' => '660e8400-e29b-41d4-a716-446655440850',
        'name' => 'Another User',
        'email' => 'another-list@example.com',
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/users')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['name' => 'API User'])
        ->assertJsonFragment(['name' => 'Another User']);
});

it('requires authentication', function (): void {
    $this->getJson('/api/users')
        ->assertUnauthorized();
});
