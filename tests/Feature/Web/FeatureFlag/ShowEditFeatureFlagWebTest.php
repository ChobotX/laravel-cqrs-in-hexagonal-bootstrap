<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('shows edit page for boolean flag', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f30',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/feature-flags/registry.schema-builder')
        ->assertStatus(200)
        ->assertSee('registry.schema-builder');
});

it('returns 403 for user without permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f32',
        'name' => 'Regular User',
        'email' => 'regular@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->get('/feature-flags/registry.schema-builder')
        ->assertStatus(403);
});

it('returns 404 for nonexistent flag', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f33',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->get('/feature-flags/nonexistent.flag')
        ->assertStatus(404);
});
