<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

/** @return array{0: UserModel, 1: UserModel} */
function memberWebUser(): array
{
    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Test Org',
        'slug' => 'test-org',
        'description' => 'Test',
    ]);

    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440980',
        'name' => 'Admin',
        'email' => 'memberadmin@test.com',
        'password' => Hash::make('password'),
    ]);

    OrganizationMemberModel::create([
        'user_id' => $admin->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440981',
        'name' => 'Target User',
        'email' => 'target@test.com',
        'password' => Hash::make('password'),
    ]);

    return [$admin, $target];
}

it('adds a member to organization', function (): void {
    [$admin, $target] = memberWebUser();

    $this->actingAs($admin)->post('/organizations/00000000-0000-0000-0000-000000000001/members', [
        '_action' => 'add_member',
        'user_id' => $target->id,
    ])->assertRedirect();

    $this->assertDatabaseHas('organization_members', [
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
    ]);
});

it('removes a member from organization', function (): void {
    [$admin, $target] = memberWebUser();

    OrganizationMemberModel::create([
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    $this->actingAs($admin)->post('/organizations/00000000-0000-0000-0000-000000000001/members', [
        '_action' => 'remove_member',
        'user_id' => $target->id,
    ])->assertRedirect();

    $this->assertDatabaseMissing('organization_members', [
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
    ]);
});
