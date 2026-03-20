<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\Organization\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function teamWebUser(): UserModel
{
    OrganizationModel::create([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Default',
        'slug' => 'default',
        'description' => 'Default org',
    ]);

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f00',
        'name' => 'Team Admin',
        'email' => 'teamadmin@test.com',
        'password' => Hash::make('password'),
    ]);

    OrganizationMemberModel::create([
        'user_id' => $user->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('shows team list page', function (): void {
    $userModel = teamWebUser();

    $this->actingAs($userModel)
        ->get('/organizations/00000000-0000-0000-0000-000000000001/teams')
        ->assertOk();
});

it('shows create team form', function (): void {
    $userModel = teamWebUser();

    $this->actingAs($userModel)
        ->get('/organizations/00000000-0000-0000-0000-000000000001/teams/create')
        ->assertOk();
});

it('creates a team via web', function (): void {
    $userModel = teamWebUser();

    $this->actingAs($userModel)
        ->post('/organizations/00000000-0000-0000-0000-000000000001/teams', [
            'name' => 'Engineering',
            'slug' => 'engineering',
            'description' => 'Engineering team',
        ])->assertRedirect(route('teams.index', '00000000-0000-0000-0000-000000000001'));

    $this->assertDatabaseHas('teams', ['name' => 'Engineering', 'slug' => 'engineering']);
});

it('creates a team with parent via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f10',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Parent Team',
        'slug' => 'parent-team',
        'description' => 'Parent',
    ]);

    $this->actingAs($userModel)
        ->post('/organizations/00000000-0000-0000-0000-000000000001/teams', [
            'name' => 'Child Team',
            'slug' => 'child-team',
            'description' => 'Child',
            'parent_team_id' => '550e8400-e29b-41d4-a716-446655440f10',
        ])->assertRedirect(route('teams.index', '00000000-0000-0000-0000-000000000001'));

    $this->assertDatabaseHas('teams', [
        'name' => 'Child Team',
        'parent_team_id' => '550e8400-e29b-41d4-a716-446655440f10',
    ]);
});

it('shows team detail page', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f20',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Show Team',
        'slug' => 'show-team',
        'description' => 'Test',
    ]);

    $response = $this->actingAs($userModel)
        ->get('/organizations/00000000-0000-0000-0000-000000000001/teams/550e8400-e29b-41d4-a716-446655440f20');
    $response->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('Show Team');
});

it('shows edit team form', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f30',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Edit Team',
        'slug' => 'edit-team',
        'description' => 'Test',
    ]);

    $this->actingAs($userModel)
        ->get('/organizations/00000000-0000-0000-0000-000000000001/teams/550e8400-e29b-41d4-a716-446655440f30/edit')
        ->assertOk();
});

it('updates a team via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f40',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Original Team',
        'slug' => 'original-team',
        'description' => 'Test',
    ]);

    $this->actingAs($userModel)
        ->put('/organizations/00000000-0000-0000-0000-000000000001/teams/550e8400-e29b-41d4-a716-446655440f40', [
            'name' => 'Updated Team',
            'slug' => 'updated-team',
            'description' => 'Updated',
        ])->assertRedirect(route('teams.index', '00000000-0000-0000-0000-000000000001'));

    $this->assertDatabaseHas('teams', ['name' => 'Updated Team', 'slug' => 'updated-team']);
});

it('deletes a team via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f50',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Delete Team',
        'slug' => 'delete-team',
        'description' => 'Test',
    ]);

    $this->actingAs($userModel)
        ->delete('/organizations/00000000-0000-0000-0000-000000000001/teams/550e8400-e29b-41d4-a716-446655440f50')
        ->assertRedirect(route('teams.index', '00000000-0000-0000-0000-000000000001'));

    $this->assertSoftDeleted('teams', ['id' => '550e8400-e29b-41d4-a716-446655440f50']);
});

it('adds a team member via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f60',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Member Team',
        'slug' => 'member-team',
        'description' => 'Test',
    ]);

    $member = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f61',
        'name' => 'New Member',
        'email' => 'newmember@test.com',
    ]);

    OrganizationMemberModel::create([
        'user_id' => $member->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    $this->actingAs($userModel)
        ->post('/organizations/00000000-0000-0000-0000-000000000001/teams/550e8400-e29b-41d4-a716-446655440f60/members', [
            '_action' => 'add_member',
            'user_id' => $member->id,
        ])->assertRedirect(route('teams.show', ['00000000-0000-0000-0000-000000000001', '550e8400-e29b-41d4-a716-446655440f60']));

    $this->assertDatabaseHas('team_members', [
        'user_id' => $member->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440f60',
    ]);
});

it('removes a team member via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f70',
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Remove Member Team',
        'slug' => 'remove-member-team',
        'description' => 'Test',
    ]);

    $member = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f71',
        'name' => 'Remove Me',
        'email' => 'removeme@test.com',
    ]);

    OrganizationMemberModel::create([
        'user_id' => $member->id,
        'organization_id' => '00000000-0000-0000-0000-000000000001',
        'joined_at' => now(),
    ]);

    App\Infrastructure\Eloquent\Organization\TeamMemberModel::create([
        'team_id' => '550e8400-e29b-41d4-a716-446655440f70',
        'user_id' => $member->id,
        'joined_at' => now(),
    ]);

    $this->actingAs($userModel)
        ->post('/organizations/00000000-0000-0000-0000-000000000001/teams/550e8400-e29b-41d4-a716-446655440f70/members', [
            '_action' => 'remove_member',
            'user_id' => $member->id,
        ])->assertRedirect(route('teams.show', ['00000000-0000-0000-0000-000000000001', '550e8400-e29b-41d4-a716-446655440f70']));

    $this->assertDatabaseMissing('team_members', [
        'user_id' => $member->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440f70',
    ]);
});

it('redirects unauthenticated to login', function (): void {
    $this->get('/organizations/00000000-0000-0000-0000-000000000001/teams')->assertRedirect('/login');
});
