<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('stores redirect query parameter as intended URL', function (): void {
    $this->get('/login?redirect=/users/create')
        ->assertStatus(200)
        ->assertSessionHas('url.intended', '/users/create');
});

it('rejects external redirect query parameter', function (): void {
    $this->get('/login?redirect=https://evil.com/phish')
        ->assertStatus(200)
        ->assertSessionMissing('url.intended');
});

it('rejects protocol-relative redirect query parameter', function (): void {
    $this->get('/login?redirect=//evil.com/phish')
        ->assertStatus(200)
        ->assertSessionMissing('url.intended');
});

it('ignores empty redirect query parameter', function (): void {
    $this->get('/login?redirect=')
        ->assertStatus(200)
        ->assertSessionMissing('url.intended');
});

it('redirects to intended URL after login with redirect parameter', function (): void {
    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440030',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->get('/login?redirect=/roles');

    $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ])->assertRedirect('/roles');
});
