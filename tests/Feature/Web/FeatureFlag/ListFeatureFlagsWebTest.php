<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows feature flags list for super admin', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f00',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/feature-flags')
        ->assertStatus(200)
        ->assertSee('registry.schema-builder');
});

it('redirects unauthenticated user to login', function (): void {
    $this->get('/feature-flags')
        ->assertRedirect('/login?'.http_build_query(['redirect' => '/feature-flags']));
});

it('returns 403 for user without permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f01',
        'name' => 'Regular User',
        'email' => 'regular@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/feature-flags')
        ->assertStatus(403);
});
