<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function searchRolesAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440800',
        'name' => 'Search Admin',
        'email' => 'searchrolesadmin@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Org',
        'slug' => 'search-roles-org',
        'description' => '',
    ]);

    OrganizationMemberModel::create([
        'user_id' => $admin->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    RoleModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440810',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Content Editor',
        'description' => 'Edits content',
        'is_system' => false,
    ]);

    RoleModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440811',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Content Viewer',
        'description' => 'Views content',
        'is_system' => false,
    ]);

    return $admin;
}

it('returns matching roles by name', function (): void {
    $userModel = searchRolesAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/roles/search?q=Editor')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Content Editor');
});

it('excludes specified role IDs', function (): void {
    $userModel = searchRolesAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/roles/search?q=Content&exclude[]=550e8400-e29b-41d4-a716-446655440810')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Content Viewer');
});

it('requires authentication', function (): void {
    $this->getJson('/internal-api/roles/search?q=test')
        ->assertUnauthorized();
});

it('returns all roles with empty query', function (): void {
    $userModel = searchRolesAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/roles/search')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('returns correct JSON structure', function (): void {
    $userModel = searchRolesAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/roles/search?q=Editor')
        ->assertOk();

    $response->assertJsonCount(1, 'data')
        ->assertJsonStructure(['data' => [['id', 'name', 'description']]]);
});

it('returns empty results for non-matching query', function (): void {
    $userModel = searchRolesAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/roles/search?q=Nonexistent')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
