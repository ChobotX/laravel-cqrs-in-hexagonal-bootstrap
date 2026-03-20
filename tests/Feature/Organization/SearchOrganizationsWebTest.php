<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function searchOrgsAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440900',
        'name' => 'Search Org Admin',
        'email' => 'searchorgsadmin@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Default Org',
        'slug' => 'default',
        'description' => '',
    ]);

    OrganizationMemberModel::create([
        'user_id' => $admin->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    OrganizationModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440910',
        'name' => 'Acme Corp',
        'slug' => 'acme-corp',
        'description' => 'Acme description',
    ]);

    OrganizationModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440911',
        'name' => 'Beta Inc',
        'slug' => 'beta-inc',
        'description' => 'Beta description',
    ]);

    return $admin;
}

it('returns matching organizations by name', function (): void {
    $userModel = searchOrgsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/organizations/search?q=Acme')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Acme Corp');
});

it('excludes specified organization IDs', function (): void {
    $userModel = searchOrgsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/organizations/search?exclude[]=550e8400-e29b-41d4-a716-446655440910')
        ->assertOk()
        ->assertJsonFragment(['name' => 'Beta Inc'])
        ->assertJsonMissing(['name' => 'Acme Corp']);
});

it('returns all organizations with empty query', function (): void {
    $userModel = searchOrgsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/organizations/search')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('requires authentication', function (): void {
    $this->getJson('/internal-api/organizations/search?q=test')
        ->assertUnauthorized();
});

it('requires organizations.management.read permission', function (): void {
    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Default',
        'slug' => 'default',
        'description' => '',
    ]);

    $role = $this->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'No Org Perm',
        'Cannot read orgs',
        ['users.list.read' => 'all'],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440920',
        'name' => 'No Perm User',
        'email' => 'noperm-searchorg@test.com',
        'password' => Hash::make('password'),
    ]);
    $this->assignRole($user->id, $role->id, '00000000-0000-0000-0000-000000000001');

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    $this->actingAs($user)
        ->getJson('/internal-api/organizations/search?q=test')
        ->assertForbidden();
});

it('returns correct JSON structure', function (): void {
    $userModel = searchOrgsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/organizations/search?q=Acme')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'email']]]);
});
