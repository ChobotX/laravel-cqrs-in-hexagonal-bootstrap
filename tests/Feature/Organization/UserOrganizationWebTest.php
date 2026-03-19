<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

/** @return array{0: UserModel, 1: UserModel} */
function userOrgWebUser(): array
{
    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Org A',
        'slug' => 'org-aa',
        'description' => 'Org A',
    ]);

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000002',
        'name' => 'Org B',
        'slug' => 'org-bb',
        'description' => 'Org B',
    ]);

    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440990',
        'name' => 'Admin',
        'email' => 'userorgadmin@test.com',
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
        'id' => '550e8400-e29b-41d4-a716-446655440991',
        'name' => 'Target',
        'email' => 'userorg-target@test.com',
        'password' => Hash::make('password'),
    ]);

    return [$admin, $target];
}

it('adds user to organization from user permissions page', function (): void {
    [$admin, $target] = userOrgWebUser();

    $this->actingAs($admin)->post(sprintf('/users/%s/organizations', $target->id), [
        '_action' => 'add_organization',
        'organization_id' => '00000000-0000-0000-0000-000000000002',
    ])->assertRedirect();

    $this->assertDatabaseHas('organization_members', [
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000002',
    ]);
});

it('removes user from organization from user permissions page', function (): void {
    [$admin, $target] = userOrgWebUser();

    OrganizationMemberModel::create([
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    $this->actingAs($admin)->post(sprintf('/users/%s/organizations', $target->id), [
        '_action' => 'remove_organization',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
    ])->assertRedirect();

    $this->assertDatabaseMissing('organization_members', [
        'user_id' => $target->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
    ]);
});
