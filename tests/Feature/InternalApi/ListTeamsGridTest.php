<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Label\LabelModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function teamsGridAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544b000',
        'name' => 'Teams Admin',
        'email' => 'teams-admin@example.com',
        'password' => Hash::make('password123'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    return $admin;
}

it('returns teams as json', function (): void {
    $userModel = teamsGridAdmin();

    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b001', 'name' => 'Engineering', 'slug' => 'engineering', 'description' => 'Eng team']);

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Engineering')
        ->assertJsonPath('meta.total', 1)
        ->assertJsonStructure([
            'data' => [['id', 'name', 'slug', 'description', 'parent_name', 'labels', 'show_url', 'edit_url', 'delete_url']],
            'meta' => ['current_page', 'per_page', 'total', 'total_pages'],
            'permissions' => ['can_create', 'can_read', 'can_update', 'can_delete'],
        ]);
});

it('redirects unauthenticated user', function (): void {
    $this->getJson('/internal-api/teams/list')
        ->assertUnauthorized();
});

it('sorts teams by name ascending', function (): void {
    $userModel = teamsGridAdmin();

    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b010', 'name' => 'Zulu Team', 'slug' => 'zulu-team', 'description' => 'Z']);
    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b011', 'name' => 'Alpha Team', 'slug' => 'alpha-team', 'description' => 'A']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list?sort=name&direction=asc')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toEqual(['Alpha Team', 'Zulu Team']);
});

it('sorts teams by slug', function (): void {
    $userModel = teamsGridAdmin();

    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b020', 'name' => 'First', 'slug' => 'zulu-slug', 'description' => '']);
    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b021', 'name' => 'Second', 'slug' => 'alpha-slug', 'description' => '']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list?sort=slug&direction=asc')
        ->assertOk();

    $slugs = array_column($response->json('data'), 'slug');
    expect($slugs)->toEqual(['alpha-slug', 'zulu-slug']);
});

it('ignores invalid sort column', function (): void {
    $userModel = teamsGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list?sort=invalid&direction=asc')
        ->assertOk();
});

it('filters teams by search', function (): void {
    $userModel = teamsGridAdmin();

    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b030', 'name' => 'Engineering', 'slug' => 'engineering', 'description' => '']);
    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b031', 'name' => 'Marketing', 'slug' => 'marketing', 'description' => '']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list?search=Engineer')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('Engineering');
    expect($names)->not->toContain('Marketing');
});

it('includes parent team name when available', function (): void {
    $userModel = teamsGridAdmin();

    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b040', 'name' => 'Parent Team', 'slug' => 'parent', 'description' => '']);
    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b041', 'name' => 'Child Team', 'slug' => 'child', 'description' => '', 'parent_team_id' => '550e8400-e29b-41d4-a716-44665544b040']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list?sort=name&direction=asc')
        ->assertOk();

    /** @var list<array<string, mixed>> $data */
    $data = $response->json('data');
    /** @var array<string, mixed> $child */
    $child = collect($data)->firstWhere('name', 'Child Team');
    expect($child['parent_name'])->toBe('Parent Team');
});

it('includes labels when user has permission', function (): void {
    $userModel = teamsGridAdmin();

    TeamModel::create(['id' => '550e8400-e29b-41d4-a716-44665544b050', 'name' => 'Labeled Team', 'slug' => 'labeled', 'description' => '']);
    $label = LabelModel::create(['id' => '00000000-0000-0000-0000-0000000000b1', 'namespace' => 'teams', 'name' => 'Team Label']);
    DB::table('label_assignments')->insert(['label_id' => $label->id, 'labelable_id' => '550e8400-e29b-41d4-a716-44665544b050']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list')
        ->assertOk();

    /** @var list<array<string, mixed>> $data */
    $data = $response->json('data');
    /** @var array<string, mixed> $team */
    $team = collect($data)->firstWhere('name', 'Labeled Team');
    /** @var list<array<string, string>> $labels */
    $labels = $team['labels'];
    expect($labels)->toHaveCount(1);
    expect($labels[0]['name'])->toBe('Team Label');
});

it('paginates results', function (): void {
    $userModel = teamsGridAdmin();

    for ($i = 1; $i <= 20; $i++) {
        TeamModel::create([
            'id' => sprintf('550e8400-e29b-41d4-a716-4466554b%04d', $i),
            'name' => 'Team '.$i,
            'slug' => 'team-'.$i,
            'description' => '',
        ]);
    }

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list?page=2&per_page=15')
        ->assertOk()
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.total', 20);
});

it('returns permissions in response', function (): void {
    $userModel = teamsGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/teams/list')
        ->assertOk()
        ->assertJsonPath('permissions.can_create', true)
        ->assertJsonPath('permissions.can_update', true)
        ->assertJsonPath('permissions.can_delete', true);
});
