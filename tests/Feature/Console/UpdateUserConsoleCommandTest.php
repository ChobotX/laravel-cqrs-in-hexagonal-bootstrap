<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;

it('updates a user', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440530',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->artisan('user:update', [
        'id' => '550e8400-e29b-41d4-a716-446655440530',
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ])
        ->expectsOutputToContain('User updated.')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'id' => '550e8400-e29b-41d4-a716-446655440530',
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ]);
});

it('fails with error when user not found', function (): void {
    $this->artisan('user:update', [
        'id' => '550e8400-e29b-41d4-a716-446655440531',
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
    ])
        ->expectsOutputToContain('User with id [550e8400-e29b-41d4-a716-446655440531] not found.')
        ->assertFailed();
});
