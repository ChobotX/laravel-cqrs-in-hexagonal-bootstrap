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
        ->assertRedirect('/login');
});

it('passes referer as redirect query parameter on CSRF token mismatch', function (): void {
    $referer = url('/users/create');

    $this->post('/test-csrf-expired', [], ['HTTP_REFERER' => $referer])
        ->assertRedirect('/login?'.http_build_query(['redirect' => $referer]));
});

it('does not include external referer in redirect', function (): void {
    $this->post('/test-csrf-expired', [], ['HTTP_REFERER' => 'https://evil.com/phish'])
        ->assertRedirect('/login');
});

it('handles missing referer on CSRF token mismatch', function (): void {
    $this->post('/test-csrf-expired')
        ->assertRedirect('/login');
});

it('returns JSON 419 for AJAX requests with expired CSRF token', function (): void {
    $this->postJson('/test-csrf-expired')
        ->assertStatus(419)
        ->assertJson(['redirect' => '/login']);
});

it('redirects authenticated user back with csrf_expired query param and input on CSRF token mismatch', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440020',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->from('/users/create')
        ->post('/test-csrf-expired', ['name' => 'Test Input'])
        ->assertRedirect('/users/create?csrf_expired=1')
        ->assertSessionHasInput('name', 'Test Input');
});

it('renders error toast from csrf_expired query parameter', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440021',
        'name' => 'Jane Doe',
        'email' => 'jane2@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/dashboard?csrf_expired=1')
        ->assertOk()
        ->assertSee('app-flash-data')
        ->assertSee(__('messages.auth.session_expired'));
});
