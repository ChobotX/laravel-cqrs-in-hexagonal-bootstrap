<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('logs out an authenticated user', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440010',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/login');

    $this->get('/users')->assertRedirect('/login?'.http_build_query(['redirect' => '/users']));
});

it('redirects unauthenticated user from logout', function (): void {
    $this->post('/logout')
        ->assertRedirect('/login');
});
