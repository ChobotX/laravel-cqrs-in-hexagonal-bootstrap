<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Team\TeamMemberModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

function treeTestAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f00',
        'name' => 'Tree Admin',
        'email' => 'treeadmin@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    return $admin;
}

function treeTestTeams(): void
{
    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f10',
        'name' => 'Company',
        'slug' => 'company',
        'description' => 'Root team',
    ]);

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f11',
        'name' => 'Engineering',
        'slug' => 'engineering',
        'description' => 'Eng team',
        'parent_team_id' => '550e8400-e29b-41d4-a716-446655440f10',
    ]);

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f12',
        'name' => 'Backend',
        'slug' => 'backend',
        'description' => 'Backend team',
        'parent_team_id' => '550e8400-e29b-41d4-a716-446655440f11',
    ]);
}

it('returns team tree with correct JSON structure', function (): void {
    $userModel = treeTestAdmin();
    treeTestTeams();

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/tree')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'parentId',
                    'name',
                    'slug',
                    'memberCount',
                    'members',
                ],
            ],
        ])
        ->assertJsonCount(3, 'data');
});

it('sets empty parentId for root teams', function (): void {
    $userModel = treeTestAdmin();
    treeTestTeams();

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/tree')
        ->assertOk()
        ->assertJsonPath('data.0.parentId', '');
});

it('preserves parent references for child teams', function (): void {
    $userModel = treeTestAdmin();
    treeTestTeams();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/teams/tree')
        ->assertOk();

    /** @var list<array{name: string, parentId: string}> $data */
    $data = $response->json('data');

    $engineering = null;

    foreach ($data as $node) {
        if ($node['name'] === 'Engineering') {
            $engineering = $node;

            break;
        }
    }

    assert($engineering !== null);
    expect($engineering['parentId'])->toBe('550e8400-e29b-41d4-a716-446655440f10');
});

it('includes members with their roles', function (): void {
    $userModel = treeTestAdmin();
    treeTestTeams();

    $member = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f20',
        'name' => 'Team Member',
        'email' => 'member@test.com',
        'password' => Hash::make('password'),
    ]);

    TeamMemberModel::create([
        'id' => Str::uuid()->toString(),
        'user_id' => $member->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440f10',
        'joined_at' => '2024-01-01 00:00:00',
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/tree')
        ->assertOk()
        ->assertJsonPath('data.0.memberCount', 1)
        ->assertJsonCount(1, 'data.0.members')
        ->assertJsonPath('data.0.members.0.name', 'Team Member');
});

it('redirects unauthenticated users', function (): void {
    $this->getJson('/internal-api/teams/tree')
        ->assertUnauthorized();
});

it('omits roles when user lacks users.roles.read permission', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f40',
        'name' => 'No Roles User',
        'email' => 'noroles@test.com',
        'password' => Hash::make('password'),
    ]);

    $role = test()->seedRoleWithPermissions('Team Only', 'Can view teams but not roles', [
        'teams.management.read' => 'all',
    ]);
    test()->assignRole($user->id, $role->id);

    treeTestTeams();

    $member = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f41',
        'name' => 'Member User',
        'email' => 'membernoread@test.com',
        'password' => Hash::make('password'),
    ]);

    TeamMemberModel::create([
        'id' => Str::uuid()->toString(),
        'user_id' => $member->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440f10',
        'joined_at' => '2024-01-01 00:00:00',
    ]);

    $this->actingAs($user)
        ->getJson('/internal-api/teams/tree')
        ->assertOk()
        ->assertJsonPath('data.0.members.0.roles', []);
});

it('sets parentId to empty when parent is filtered out by scope', function (): void {
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f30',
        'name' => 'Scoped User',
        'email' => 'scoped@test.com',
        'password' => Hash::make('password'),
    ]);

    $role = test()->seedRoleWithPermissions('Team Viewer', 'Can view teams with team scope', [
        'teams.management.read' => 'team',
        'users.roles.read' => 'all',
    ]);
    test()->assignRole($user->id, $role->id);

    treeTestTeams();

    TeamMemberModel::create([
        'id' => Str::uuid()->toString(),
        'user_id' => $user->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440f11',
        'joined_at' => '2024-01-01 00:00:00',
    ]);

    $response = $this->actingAs($user)
        ->getJson('/internal-api/teams/tree')
        ->assertOk();

    /** @var list<array{name: string, parentId: string}> $data */
    $data = $response->json('data');

    $names = array_column($data, 'name');

    expect($names)->toContain('Engineering', 'Backend');
    expect($names)->not()->toContain('Company');

    $engineering = null;

    foreach ($data as $node) {
        if ($node['name'] === 'Engineering') {
            $engineering = $node;

            break;
        }
    }

    assert($engineering !== null);
    expect($engineering['parentId'])->toBe('');
});
