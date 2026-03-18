<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Laravel\Sanctum\Sanctum;

it('starts impersonation via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440940', 'name' => 'Admin', 'email' => 'impa@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $target = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440941', 'name' => 'Target', 'email' => 'impb@test.com']);

    $this->postJson('/api/impersonate/'.$target->id)->assertOk();

    $this->assertDatabaseHas('impersonation_sessions', [
        'impersonator_user_id' => $admin->id,
        'impersonated_user_id' => $target->id,
    ]);
});

it('stops impersonation via API', function (): void {
    $this->seedSuperAdminRole();
    $admin = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440942', 'name' => 'Admin', 'email' => 'impc@test.com']);
    $this->assignSuperAdmin($admin->id);
    Sanctum::actingAs($admin);

    $this->postJson('/api/stop-impersonation')->assertOk();
});

it('returns 401 on start impersonation unauthenticated', function (): void {
    $this->postJson('/api/impersonate/550e8400-e29b-41d4-a716-446655440944')->assertUnauthorized();
});

it('returns 401 on stop impersonation unauthenticated', function (): void {
    $this->postJson('/api/stop-impersonation')->assertUnauthorized();
});
