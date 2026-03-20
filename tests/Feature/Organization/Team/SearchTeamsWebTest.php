<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\Organization\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function searchTeamsAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e00',
        'name' => 'Search Teams Admin',
        'email' => 'searchteamsadmin@test.com',
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

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e10',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Engineering',
        'slug' => 'engineering',
        'description' => '',
    ]);

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e11',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Design',
        'slug' => 'design',
        'description' => '',
    ]);

    return $admin;
}

it('returns matching teams by name', function (): void {
    $userModel = searchTeamsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/search?q=Engineer')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Engineering');
});

it('excludes specified team IDs', function (): void {
    $userModel = searchTeamsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/search?exclude[]=550e8400-e29b-41d4-a716-446655440e10')
        ->assertOk()
        ->assertJsonFragment(['name' => 'Design'])
        ->assertJsonMissing(['name' => 'Engineering']);
});

it('returns all teams with empty query', function (): void {
    $userModel = searchTeamsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/search')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('returns correct JSON structure', function (): void {
    $userModel = searchTeamsAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/search?q=Engineering')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name']]]);
});
