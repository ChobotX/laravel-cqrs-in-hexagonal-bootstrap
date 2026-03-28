<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::post('/test-csrf-expired', function (): never {
        throw new TokenMismatchException;
    })->middleware('web');
});

it('redirects unauthenticated user to login on CSRF token mismatch', function (): void {
    $this->post('/test-csrf-expired')
        ->assertRedirect('/login')
        ->assertSessionHas('error');
});

it('stores referer as intended URL on CSRF token mismatch', function (): void {
    $referer = url('/users/create');

    $this->post('/test-csrf-expired', [], ['HTTP_REFERER' => $referer])
        ->assertRedirect('/login')
        ->assertSessionHas('url.intended', $referer);
});

it('does not store external referer as intended URL', function (): void {
    $this->post('/test-csrf-expired', [], ['HTTP_REFERER' => 'https://evil.com/phish'])
        ->assertRedirect('/login')
        ->assertSessionMissing('url.intended');
});

it('handles missing referer on CSRF token mismatch', function (): void {
    $this->post('/test-csrf-expired')
        ->assertRedirect('/login')
        ->assertSessionMissing('url.intended');
});

it('returns JSON 419 for AJAX requests with expired CSRF token', function (): void {
    $this->postJson('/test-csrf-expired')
        ->assertStatus(419)
        ->assertJson(['redirect' => '/login']);
});

it('redirects authenticated user back on CSRF token mismatch', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440020',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->from('/users/create')
        ->post('/test-csrf-expired')
        ->assertRedirect('/users/create')
        ->assertSessionHas('error');
});
