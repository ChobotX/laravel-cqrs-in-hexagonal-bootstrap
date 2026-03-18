<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;

it('soft-deletes a user', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440540',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->artisan('user:delete', ['id' => '550e8400-e29b-41d4-a716-446655440540'])
        ->expectsOutputToContain('User deleted.')
        ->assertSuccessful();

    $this->assertSoftDeleted('users', [
        'id' => '550e8400-e29b-41d4-a716-446655440540',
    ]);
});

it('fails with error when user not found', function (): void {
    $this->artisan('user:delete', ['id' => '550e8400-e29b-41d4-a716-446655440541'])
        ->expectsOutputToContain('User with id [550e8400-e29b-41d4-a716-446655440541] not found.')
        ->assertFailed();
});
