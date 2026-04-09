<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function toggleFlagAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544ff10',
        'name' => 'Toggle Admin',
        'email' => 'toggle-admin@example.com',
        'password' => Hash::make('password123'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    return $admin;
}

it('toggles feature flag enabled state', function (): void {
    $userModel = toggleFlagAdmin();

    $this->actingAs($userModel)
        ->patchJson('/internal-api/feature-flags/registry.schema-builder/toggle', ['enabled' => false])
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('feature_flag_overrides', [
        'key' => 'registry.schema-builder',
        'enabled' => false,
    ]);
});

it('enables feature flag via toggle', function (): void {
    $userModel = toggleFlagAdmin();

    $this->actingAs($userModel)
        ->patchJson('/internal-api/feature-flags/registry.schema-builder/toggle', ['enabled' => true])
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('feature_flag_overrides', [
        'key' => 'registry.schema-builder',
        'enabled' => true,
    ]);
});

it('returns 401 for unauthenticated user', function (): void {
    $this->patchJson('/internal-api/feature-flags/registry.schema-builder/toggle', ['enabled' => false])
        ->assertUnauthorized();
});

it('returns 403 for user without permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544ff11',
        'name' => 'Regular User',
        'email' => 'regular-toggle@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->patchJson('/internal-api/feature-flags/registry.schema-builder/toggle', ['enabled' => false])
        ->assertForbidden();
});
