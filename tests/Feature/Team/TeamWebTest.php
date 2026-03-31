<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function teamWebUser(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f00',
        'name' => 'Team Admin',
        'email' => 'teamadmin@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

it('shows team list page', function (): void {
    $userModel = teamWebUser();

    $this->actingAs($userModel)
        ->get('/teams')
        ->assertOk();
});

it('shows create team form', function (): void {
    $userModel = teamWebUser();

    $this->actingAs($userModel)
        ->get('/teams/create')
        ->assertOk();
});

it('creates a team via web', function (): void {
    $userModel = teamWebUser();

    $this->actingAs($userModel)
        ->post('/teams', [
            'name' => 'Engineering',
            'slug' => 'engineering',
            'description' => 'Engineering team',
        ])->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('teams', ['name' => 'Engineering', 'slug' => 'engineering']);
});

it('creates a team with parent via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f10',
        'name' => 'Parent Team',
        'slug' => 'parent-team',
        'description' => 'Parent',
    ]);

    $this->actingAs($userModel)
        ->post('/teams', [
            'name' => 'Child Team',
            'slug' => 'child-team',
            'description' => 'Child',
            'parent_team_id' => '550e8400-e29b-41d4-a716-446655440f10',
        ])->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('teams', [
        'name' => 'Child Team',
        'parent_team_id' => '550e8400-e29b-41d4-a716-446655440f10',
    ]);
});

it('shows team detail page', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f20',
        'name' => 'Show Team',
        'slug' => 'show-team',
        'description' => 'Test',
    ]);

    $response = $this->actingAs($userModel)
        ->get('/teams/550e8400-e29b-41d4-a716-446655440f20');
    $response->assertOk();

    $content = $response->getContent();
    expect($content)->toContain('Show Team');
});

it('shows edit team form', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f30',
        'name' => 'Edit Team',
        'slug' => 'edit-team',
        'description' => 'Test',
    ]);

    $this->actingAs($userModel)
        ->get('/teams/550e8400-e29b-41d4-a716-446655440f30/edit')
        ->assertOk();
});

it('updates a team via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f40',
        'name' => 'Original Team',
        'slug' => 'original-team',
        'description' => 'Test',
    ]);

    $this->actingAs($userModel)
        ->put('/teams/550e8400-e29b-41d4-a716-446655440f40', [
            'name' => 'Updated Team',
            'slug' => 'updated-team',
            'description' => 'Updated',
        ])->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('teams', ['name' => 'Updated Team', 'slug' => 'updated-team']);
});

it('deletes a team via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f50',
        'name' => 'Delete Team',
        'slug' => 'delete-team',
        'description' => 'Test',
    ]);

    $this->actingAs($userModel)
        ->delete('/teams/550e8400-e29b-41d4-a716-446655440f50')
        ->assertRedirect(route('teams.index'));

    $this->assertSoftDeleted('teams', ['id' => '550e8400-e29b-41d4-a716-446655440f50']);
});

it('adds a team member via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f60',
        'name' => 'Member Team',
        'slug' => 'member-team',
        'description' => 'Test',
    ]);

    $member = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f61',
        'name' => 'New Member',
        'email' => 'newmember@test.com',
    ]);

    $this->actingAs($userModel)
        ->post('/teams/550e8400-e29b-41d4-a716-446655440f60/members', [
            '_action' => 'add_member',
            'user_id' => $member->id,
        ])->assertRedirect(route('teams.show', '550e8400-e29b-41d4-a716-446655440f60'));

    $this->assertDatabaseHas('team_members', [
        'user_id' => $member->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440f60',
    ]);
});

it('removes a team member via web', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f70',
        'name' => 'Remove Member Team',
        'slug' => 'remove-member-team',
        'description' => 'Test',
    ]);

    $member = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440f71',
        'name' => 'Remove Me',
        'email' => 'removeme@test.com',
    ]);

    App\Infrastructure\Eloquent\Team\TeamMemberModel::create([
        'team_id' => '550e8400-e29b-41d4-a716-446655440f70',
        'user_id' => $member->id,
        'joined_at' => now(),
    ]);

    $this->actingAs($userModel)
        ->post('/teams/550e8400-e29b-41d4-a716-446655440f70/members', [
            '_action' => 'remove_member',
            'user_id' => $member->id,
        ])->assertRedirect(route('teams.show', '550e8400-e29b-41d4-a716-446655440f70'));

    $this->assertDatabaseMissing('team_members', [
        'user_id' => $member->id,
        'team_id' => '550e8400-e29b-41d4-a716-446655440f70',
    ]);
});

it('sorts teams by name ascending via query params', function (): void {
    $userModel = teamWebUser();

    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c080', 'name' => 'Zulu Team', 'slug' => 'zulu-team', 'description' => 'Z']);
    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c081', 'name' => 'Alpha Team', 'slug' => 'alpha-team', 'description' => 'A']);

    $this->actingAs($userModel)
        ->get('/teams?sort=name&direction=asc')
        ->assertOk()
        ->assertSeeInOrder(['Alpha Team', 'Zulu Team']);
});

it('sorts teams by slug via query params', function (): void {
    $userModel = teamWebUser();

    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c082', 'name' => 'First By Name', 'slug' => 'zulu-slug', 'description' => 'Z']);
    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c083', 'name' => 'Second By Name', 'slug' => 'alpha-slug', 'description' => 'A']);

    $this->actingAs($userModel)
        ->get('/teams?sort=slug&direction=asc')
        ->assertOk()
        ->assertSeeInOrder(['alpha-slug', 'zulu-slug']);
});

it('ignores invalid sort column for teams', function (): void {
    $userModel = teamWebUser();

    $this->actingAs($userModel)
        ->get('/teams?sort=invalid&direction=asc')
        ->assertOk();
});

it('redirects unauthenticated to login', function (): void {
    $this->get('/teams')->assertRedirect('/login?'.http_build_query(['redirect' => '/teams']));
});

it('redirects to page 1 when requested page exceeds total pages', function (): void {
    $userModel = teamWebUser();

    $this->actingAs($userModel)
        ->get('/teams?page=5&per_page=15&sort=name&direction=asc')
        ->assertRedirect('/teams?page=1&per_page=15&sort=name&direction=asc');
});

it('skips label sync when user lacks labels.management.read', function (): void {
    $role = test()->seedRoleWithPermissions(
        'Team Only Editor',
        'Can edit teams but not labels',
        ['teams.management.read' => 'all', 'teams.management.update' => 'all'],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fc0',
        'name' => 'No Labels Perm',
        'email' => 'nolabels-perm@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->assignRole($user->id, $role->id);

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fc1',
        'name' => 'Skip Labels Team',
        'slug' => 'skip-labels-team',
        'description' => 'Test',
    ]);

    $this->actingAs($user)
        ->put('/teams/550e8400-e29b-41d4-a716-446655440fc1', [
            'name' => 'Skip Labels Team',
            'slug' => 'skip-labels-team',
            'description' => 'Test',
            'labels' => ['550e8400-e29b-41d4-a716-446655440fb0'],
        ])->assertRedirect(route('teams.index'));

    $this->assertDatabaseMissing('label_assignments', [
        'labelable_id' => '550e8400-e29b-41d4-a716-446655440fc1',
    ]);
});

it('assigns labels to a team via form submission', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fa0',
        'name' => 'Label Team',
        'slug' => 'label-team',
        'description' => 'Team with labels',
    ]);

    App\Infrastructure\Eloquent\Label\LabelModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fb0',
        'namespace' => 'teams',
        'name' => 'priority',
    ]);

    $this->actingAs($userModel)
        ->put('/teams/550e8400-e29b-41d4-a716-446655440fa0', [
            'name' => 'Label Team',
            'slug' => 'label-team',
            'description' => 'Team with labels',
            'labels' => ['550e8400-e29b-41d4-a716-446655440fb0'],
        ])->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('label_assignments', [
        'label_id' => '550e8400-e29b-41d4-a716-446655440fb0',
        'labelable_id' => '550e8400-e29b-41d4-a716-446655440fa0',
    ]);
});

it('removes labels from a team via form submission', function (): void {
    $userModel = teamWebUser();

    TeamModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fa1',
        'name' => 'Unlabel Team',
        'slug' => 'unlabel-team',
        'description' => 'Team without labels',
    ]);

    App\Infrastructure\Eloquent\Label\LabelModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440fb1',
        'namespace' => 'teams',
        'name' => 'removeme',
    ]);

    Illuminate\Support\Facades\DB::table('label_assignments')->insert([
        'label_id' => '550e8400-e29b-41d4-a716-446655440fb1',
        'labelable_id' => '550e8400-e29b-41d4-a716-446655440fa1',
        'created_at' => now(),
    ]);

    $this->actingAs($userModel)
        ->put('/teams/550e8400-e29b-41d4-a716-446655440fa1', [
            'name' => 'Unlabel Team',
            'slug' => 'unlabel-team',
            'description' => 'Team without labels',
            'labels' => [],
        ])->assertRedirect(route('teams.index'));

    $this->assertDatabaseMissing('label_assignments', [
        'label_id' => '550e8400-e29b-41d4-a716-446655440fb1',
        'labelable_id' => '550e8400-e29b-41d4-a716-446655440fa1',
    ]);
});
