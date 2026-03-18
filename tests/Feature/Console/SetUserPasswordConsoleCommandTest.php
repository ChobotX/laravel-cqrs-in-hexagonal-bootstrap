<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('sets password for existing user', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440550',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->artisan('user:set-password', ['email' => 'john@example.com'])
        ->expectsQuestion('Password:', 'new-password-123')
        ->expectsOutputToContain('Password set for user: john@example.com')
        ->assertSuccessful();

    $user = UserModel::find('550e8400-e29b-41d4-a716-446655440550');
    expect(Hash::check('new-password-123', $user->password))->toBeTrue();
});

it('shows error when user not found', function (): void {
    $this->artisan('user:set-password', ['email' => 'nobody@example.com'])
        ->expectsQuestion('Password:', 'password123')
        ->expectsOutputToContain('User not found with email: nobody@example.com')
        ->assertFailed();
});

it('fails when password is empty', function (): void {
    $this->artisan('user:set-password', ['email' => 'john@example.com'])
        ->expectsQuestion('Password:', '')
        ->expectsOutputToContain('Password must not be empty.')
        ->assertFailed();
});
