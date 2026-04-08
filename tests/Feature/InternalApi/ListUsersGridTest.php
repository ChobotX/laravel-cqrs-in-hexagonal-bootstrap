<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Label\LabelModel;
use App\Infrastructure\Eloquent\Team\TeamMemberModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

function gridAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544a000',
        'name' => 'Grid Admin',
        'email' => 'grid-admin@example.com',
        'password' => Hash::make('password123'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    return $admin;
}

it('returns users as json', function (): void {
    $userModel = gridAdmin();

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544a001',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/users/list')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Grid Admin')
        ->assertJsonPath('meta.total', 2)
        ->assertJsonStructure([
            'data' => [['id', 'name', 'email', 'avatar_url', 'initials', 'roles', 'teams', 'labels', 'edit_url', 'delete_url', 'impersonate_url']],
            'meta' => ['current_page', 'per_page', 'total', 'total_pages'],
            'permissions' => ['can_create', 'can_update', 'can_delete', 'is_super_admin'],
        ]);
});

it('redirects unauthenticated user', function (): void {
    $this->getJson('/internal-api/users/list')
        ->assertUnauthorized();
});

it('sorts users by name ascending', function (): void {
    $userModel = gridAdmin();

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a010', 'name' => 'Zeta User', 'email' => 'zeta@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a011', 'name' => 'Alpha User', 'email' => 'alpha@example.com']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?sort=name&direction=asc')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toEqual(['Alpha User', 'Grid Admin', 'Zeta User']);
});

it('sorts users by name descending', function (): void {
    $userModel = gridAdmin();

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a020', 'name' => 'Zeta Desc', 'email' => 'zeta-desc@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a021', 'name' => 'Alpha Desc', 'email' => 'alpha-desc@example.com']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?sort=name&direction=desc')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toEqual(['Zeta Desc', 'Grid Admin', 'Alpha Desc']);
});

it('sorts users by email', function (): void {
    $userModel = gridAdmin();

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a030', 'name' => 'Email B', 'email' => 'b-email@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a031', 'name' => 'Email Z', 'email' => 'z-email@example.com']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?sort=email&direction=asc')
        ->assertOk();

    $emails = array_column($response->json('data'), 'email');
    expect($emails)->toEqual(['b-email@example.com', 'grid-admin@example.com', 'z-email@example.com']);
});

it('ignores invalid sort column', function (): void {
    $userModel = gridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?sort=invalid_column&direction=asc')
        ->assertOk();
});

it('filters users by search term matching name', function (): void {
    $userModel = gridAdmin();

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a040', 'name' => 'John Doe', 'email' => 'john@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a041', 'name' => 'Jane Smith', 'email' => 'jane@different.org']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?search=John')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('John Doe');
    expect($names)->not->toContain('Jane Smith');
});

it('filters users by search term matching email', function (): void {
    $userModel = gridAdmin();

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a050', 'name' => 'John Doe', 'email' => 'john@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a051', 'name' => 'Jane Smith', 'email' => 'jane@different.org']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?search=different.org')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('Jane Smith');
    expect($names)->not->toContain('John Doe');
});

it('returns all users when search is empty', function (): void {
    $userModel = gridAdmin();

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a060', 'name' => 'John Doe', 'email' => 'john@example.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a061', 'name' => 'Jane Smith', 'email' => 'jane@example.com']);

    $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?search=')
        ->assertOk()
        ->assertJsonPath('meta.total', 3);
});

it('returns empty results for unmatched search', function (): void {
    $userModel = gridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?search=nonexistent')
        ->assertOk()
        ->assertJsonPath('meta.total', 0);
});

it('handles search with special LIKE characters safely', function (): void {
    $userModel = gridAdmin();

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a070', 'name' => 'Normal User', 'email' => 'normal@example.com']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?search=%25')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->not->toContain('Normal User');
});

it('paginates results', function (): void {
    $userModel = gridAdmin();

    for ($i = 1; $i <= 20; $i++) {
        UserModel::create([
            'id' => sprintf('550e8400-e29b-41d4-a716-4466554b%04d', $i),
            'name' => 'PaginatedUser '.$i,
            'email' => 'paginated-'.$i.'@example.com',
        ]);
    }

    $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?page=1&per_page=15')
        ->assertOk()
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.per_page', 15)
        ->assertJsonPath('meta.total', 21)
        ->assertJsonPath('meta.total_pages', 2);

    $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?page=2&per_page=15')
        ->assertOk()
        ->assertJsonPath('meta.current_page', 2);
});

it('includes roles when user has permission', function (): void {
    $userModel = gridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list')
        ->assertOk();

    /** @var array<string, mixed> $firstUser */
    $firstUser = $response->json('data.0');
    expect($firstUser)->toHaveKey('roles');
    expect($firstUser['roles'])->toBeArray();
});

it('hides roles when user lacks permission', function (): void {
    $role = $this->seedRoleWithPermissions(
        'List Only',
        'Can only list users',
        ['users.list.read' => 'all'],
    );

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544a080',
        'name' => 'List Viewer',
        'email' => 'list-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $role->id);

    $response = $this->actingAs($viewer)
        ->getJson('/internal-api/users/list')
        ->assertOk();

    /** @var array<string, mixed> $firstUser */
    $firstUser = $response->json('data.0');
    expect($firstUser['roles'])->toBeNull();
});

it('includes impersonate url for super admin', function (): void {
    $userModel = gridAdmin();

    UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544a090',
        'name' => 'Other User',
        'email' => 'other@example.com',
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?sort=name&direction=asc')
        ->assertOk();

    /** @var list<array<string, mixed>> $users */
    $users = $response->json('data');
    /** @var array<string, mixed> $adminUser */
    $adminUser = collect($users)->firstWhere('name', 'Grid Admin');
    /** @var array<string, mixed> $otherUser */
    $otherUser = collect($users)->firstWhere('name', 'Other User');

    expect($adminUser['impersonate_url'])->toBeNull();
    expect($otherUser['impersonate_url'])->not->toBeNull();
});

it('includes teams and labels when user has permission', function (): void {
    $role = $this->seedRoleWithPermissions(
        'Full Viewer',
        'Can view everything',
        ['users.list.read' => 'all', 'users.roles.read' => 'all', 'teams.members.read' => 'all', 'labels.management.read' => 'all'],
    );

    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544a0a0',
        'name' => 'Full Viewer',
        'email' => 'full-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($admin->id, $role->id);

    $target = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544a0a1',
        'name' => 'Target Batch',
        'email' => 'target-batch@example.com',
    ]);

    TeamModel::create(['id' => '00000000-0000-0000-0000-0000000000f0', 'name' => 'Batch Team', 'slug' => 'batch-team', 'description' => '']);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-0000000000f0', 'user_id' => $target->id, 'joined_at' => now()]);

    $label = LabelModel::create(['id' => '00000000-0000-0000-0000-0000000000f1', 'namespace' => 'users', 'name' => 'Batch Label']);
    DB::table('label_assignments')->insert(['label_id' => $label->id, 'labelable_id' => $target->id]);

    $response = $this->actingAs($admin)
        ->getJson('/internal-api/users/list?sort=name&direction=desc')
        ->assertOk();

    /** @var list<array<string, mixed>> $data */
    $data = $response->json('data');
    /** @var array<string, mixed> $targetData */
    $targetData = collect($data)->firstWhere('name', 'Target Batch');
    /** @var list<array<string, string>> $teams */
    $teams = $targetData['teams'];
    expect($teams)->toHaveCount(1);
    expect($teams[0]['name'])->toBe('Batch Team');
    /** @var list<array<string, string>> $labels */
    $labels = $targetData['labels'];
    expect($labels)->toHaveCount(1);
    expect($labels[0]['name'])->toBe('Batch Label');
});

it('team-scoped user only sees teammates', function (): void {
    TeamModel::create(['id' => '00000000-0000-0000-0000-000000000090', 'name' => 'Alpha', 'slug' => 'alpha', 'description' => '']);
    TeamModel::create(['id' => '00000000-0000-0000-0000-000000000091', 'name' => 'Beta', 'slug' => 'beta', 'description' => '']);

    $role = $this->seedRoleWithPermissions('Team Scoped', 'Team scope', ['users.list.read' => 'team', 'users.roles.read' => 'team']);

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e50',
        'name' => 'Team Viewer',
        'email' => 'team-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $role->id);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-000000000090', 'user_id' => $viewer->id, 'joined_at' => now()]);

    $teammate = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440e51', 'name' => 'Teammate Alpha', 'email' => 'teammate@example.com']);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-000000000090', 'user_id' => $teammate->id, 'joined_at' => now()]);

    $outsider = UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440e52', 'name' => 'Outsider Beta', 'email' => 'outsider@example.com']);
    TeamMemberModel::create(['team_id' => '00000000-0000-0000-0000-000000000091', 'user_id' => $outsider->id, 'joined_at' => now()]);

    $response = $this->actingAs($viewer)
        ->getJson('/internal-api/users/list')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('Teammate Alpha');
    expect($names)->toContain('Team Viewer');
    expect($names)->not->toContain('Outsider Beta');
});

it('own-scoped user only sees themselves', function (): void {
    $role = $this->seedRoleWithPermissions('Own Scoped', 'Own scope', ['users.list.read' => 'own']);

    $viewer = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e60',
        'name' => 'Own Viewer',
        'email' => 'own-viewer@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->assignRole($viewer->id, $role->id);

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-446655440e61', 'name' => 'Other Own User', 'email' => 'other-own@example.com']);

    $response = $this->actingAs($viewer)
        ->getJson('/internal-api/users/list')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('Own Viewer');
    expect($names)->not->toContain('Other Own User');
});

it('combines search with sorting', function (): void {
    $userModel = gridAdmin();

    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a0b0', 'name' => 'Zoe Test', 'email' => 'zoe@test.com']);
    UserModel::create(['id' => '550e8400-e29b-41d4-a716-44665544a0b1', 'name' => 'Amy Test', 'email' => 'amy@test.com']);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/users/list?search=test&sort=name&direction=asc')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('Amy Test');
    expect($names)->toContain('Zoe Test');
    expect(array_search('Amy Test', $names, true))->toBeLessThan(array_search('Zoe Test', $names, true));
});

it('returns permissions in response', function (): void {
    $userModel = gridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/users/list')
        ->assertOk()
        ->assertJsonPath('permissions.can_create', true)
        ->assertJsonPath('permissions.can_update', true)
        ->assertJsonPath('permissions.can_delete', true)
        ->assertJsonPath('permissions.is_super_admin', true);
});
