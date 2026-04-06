<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

it('shows reset password form', function (): void {
    $this->get('/reset-password/some-token?email=user@example.com')
        ->assertOk()
        ->assertViewIs('auth.reset-password')
        ->assertSee('some-token')
        ->assertSee('user@example.com');
});

it('shows reset password form without email', function (): void {
    $this->get('/reset-password/some-token')
        ->assertOk()
        ->assertViewIs('auth.reset-password');
});

it('resets password and logs in user', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440730',
        'name' => 'Reset User',
        'email' => 'reset@example.com',
        'password' => Hash::make('oldpassword1'),
    ]);

    $token = Password::broker()->createToken($user);

    $this->post('/reset-password', [
        'email' => 'reset@example.com',
        'token' => $token,
        'password' => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])->assertRedirect('/users')
        ->assertSessionHas('success');

    $this->assertAuthenticated();
    $this->assertAuthenticatedAs($user);

    $updated = UserModel::find('550e8400-e29b-41d4-a716-446655440730');
    expect(Hash::check('newpassword1', $updated->password))->toBeTrue();
});

it('validates email is required for password reset', function (): void {
    $this->post('/reset-password', [
        'token' => 'some-token',
        'password' => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])->assertSessionHasErrors(['email']);
});

it('validates token is required for password reset', function (): void {
    $this->post('/reset-password', [
        'email' => 'reset@example.com',
        'password' => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])->assertSessionHasErrors(['token']);
});

it('validates password is required for password reset', function (): void {
    $this->post('/reset-password', [
        'email' => 'reset@example.com',
        'token' => 'some-token',
    ])->assertSessionHasErrors(['password']);
});

it('validates password minimum length for password reset', function (): void {
    $this->post('/reset-password', [
        'email' => 'reset@example.com',
        'token' => 'some-token',
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors(['password']);
});

it('validates password confirmation for password reset', function (): void {
    $this->post('/reset-password', [
        'email' => 'reset@example.com',
        'token' => 'some-token',
        'password' => 'newpassword1',
        'password_confirmation' => 'different11',
    ])->assertSessionHasErrors(['password']);
});

it('validates email format for password reset', function (): void {
    $this->post('/reset-password', [
        'email' => 'not-an-email',
        'token' => 'some-token',
        'password' => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])->assertSessionHasErrors(['email']);
});

it('validates email format for show reset password', function (): void {
    $this->get('/reset-password/some-token?email=not-an-email')
        ->assertSessionHasErrors(['email']);
});

it('redirects authenticated user away from reset password', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440731',
        'name' => 'Authed Reset',
        'email' => 'authed-reset@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/reset-password/some-token')
        ->assertRedirect();
});
