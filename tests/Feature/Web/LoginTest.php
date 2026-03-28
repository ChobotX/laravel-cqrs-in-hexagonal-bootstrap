<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows the login page', function (): void {
    $this->get('/login')
        ->assertStatus(200)
        ->assertSee('Login');
});

it('logs in with valid credentials', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440001',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ])->assertRedirect('/users');

    $this->assertAuthenticated();
});

it('rejects invalid credentials', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440002',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'wrong-password',
    ])->assertRedirect()
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('redirects authenticated user away from login', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440003',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect();
});

it('redirects to intended URL after login', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440004',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->get('/roles')->assertRedirect('/login');

    $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ])->assertRedirect('/roles');
});

it('validates login input', function (): void {
    $this->post('/login', [
        'email' => '',
        'password' => '',
    ])->assertSessionHasErrors(['email', 'password']);
});
