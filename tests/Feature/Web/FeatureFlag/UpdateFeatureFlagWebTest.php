<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('updates boolean feature flag via toggle', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f10',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->put('/feature-flags/registry.schema-builder', ['enabled' => '0'])
        ->assertRedirect('/feature-flags');

    $this->assertDatabaseHas('feature_flag_overrides', [
        'key' => 'registry.schema-builder',
        'enabled' => false,
        'value' => '0',
    ]);
});

it('enables boolean feature flag via toggle', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f11',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->put('/feature-flags/registry.schema-builder', ['enabled' => '1'])
        ->assertRedirect('/feature-flags');

    $this->assertDatabaseHas('feature_flag_overrides', [
        'key' => 'registry.schema-builder',
        'enabled' => true,
        'value' => '1',
    ]);
});

it('returns 403 for user without permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f12',
        'name' => 'Regular User',
        'email' => 'regular@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->put('/feature-flags/registry.schema-builder', ['enabled' => '1'])
        ->assertStatus(403);
});

it('rejects invalid value and redirects back with errors', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f13',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $this->actingAs($user)
        ->from('/feature-flags')
        ->put('/feature-flags/registry.schema-builder', ['enabled' => '1', 'value' => 'invalid'])
        ->assertRedirect('/feature-flags')
        ->assertSessionHasErrors('message');
});
