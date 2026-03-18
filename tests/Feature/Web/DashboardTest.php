<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('redirects authenticated user to /users', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440600',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/')
        ->assertRedirect('/users');
});

it('redirects unauthenticated user to login', function (): void {
    $this->get('/')
        ->assertRedirect('/login');
});
