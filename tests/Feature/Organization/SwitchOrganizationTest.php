<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function switchOrgUser(): UserModel
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

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440995',
        'name' => 'Switcher',
        'email' => 'switcher@test.com',
        'password' => Hash::make('password'),
    ]);

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '00000000-0000-0000-0000-000000000002',
        'joined_at' => now(),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('switches organization and sets cookie', function (): void {
    $userModel = switchOrgUser();

    $response = $this->actingAs($userModel)->post('/switch-organization', [
        'organization_id' => '00000000-0000-0000-0000-000000000002',
    ]);

    $response->assertRedirect();
    $response->assertCookie('organization_id', '00000000-0000-0000-0000-000000000002');
});

it('does not switch to non-member organization', function (): void {
    $userModel = switchOrgUser();

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000003',
        'name' => 'No Access',
        'slug' => 'no-access',
        'description' => 'No access',
    ]);

    $response = $this->actingAs($userModel)->post('/switch-organization', [
        'organization_id' => '00000000-0000-0000-0000-000000000003',
    ]);

    $response->assertRedirect();
    $response->assertCookieMissing('organization_id');
});
