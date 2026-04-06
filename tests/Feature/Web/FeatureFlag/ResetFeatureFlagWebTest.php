<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\FeatureFlag\FeatureFlagOverrideModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

it('resets feature flag override', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f20',
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignSuperAdmin($user->id);

    $model = new FeatureFlagOverrideModel;
    $model->key = 'registry.schema-builder';
    $model->value = '1';
    $model->save();

    $this->actingAs($user)
        ->delete('/feature-flags/registry.schema-builder')
        ->assertRedirect('/feature-flags');

    $this->assertDatabaseMissing('feature_flag_overrides', [
        'key' => 'registry.schema-builder',
    ]);
});

it('returns 403 for user without permission', function (): void {
    $this->seedSuperAdminRole();
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f21',
        'name' => 'Regular User',
        'email' => 'regular@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->actingAs($user)
        ->delete('/feature-flags/registry.schema-builder')
        ->assertStatus(403);
});
