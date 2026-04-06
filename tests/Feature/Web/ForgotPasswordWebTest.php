<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

it('shows forgot password form', function (): void {
    $this->get('/forgot-password')
        ->assertOk()
        ->assertViewIs('auth.forgot-password');
});

it('submits forgot password and returns success', function (): void {
    Mail::fake();

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440720',
        'name' => 'Forgot User',
        'email' => 'forgot@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/forgot-password', [
        'email' => 'forgot@example.com',
    ])->assertRedirect()
        ->assertSessionHas('success');
});

it('returns success even for non-existent email', function (): void {
    Mail::fake();

    $this->post('/forgot-password', [
        'email' => 'nonexistent@example.com',
    ])->assertRedirect()
        ->assertSessionHas('success');
});

it('validates email is required', function (): void {
    $this->post('/forgot-password', [])
        ->assertSessionHasErrors(['email']);
});

it('validates email format', function (): void {
    $this->post('/forgot-password', [
        'email' => 'not-an-email',
    ])->assertSessionHasErrors(['email']);
});

it('redirects authenticated user away from forgot password', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440721',
        'name' => 'Authed Forgot',
        'email' => 'authed-forgot@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/forgot-password')
        ->assertRedirect();
});
