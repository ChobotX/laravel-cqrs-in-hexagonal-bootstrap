<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function rolesGridAdmin(): UserModel
{
    $admin = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-44665544c000',
        'name' => 'Roles Admin',
        'email' => 'roles-admin@example.com',
        'password' => Hash::make('password123'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($admin->id);

    return $admin;
}

it('returns roles as json', function (): void {
    $userModel = rolesGridAdmin();

    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c001', 'name' => 'Editor', 'description' => 'Can edit', 'is_system' => false]);

    $this->actingAs($userModel)
        ->getJson('/internal-api/roles/list')
        ->assertOk()
        ->assertJsonPath('meta.total', 2)
        ->assertJsonStructure([
            'data' => [['id', 'name', 'description', 'is_system', 'show_url', 'edit_url', 'delete_url']],
            'meta' => ['current_page', 'per_page', 'total', 'total_pages'],
            'permissions' => ['can_read', 'can_update'],
        ]);
});

it('redirects unauthenticated user', function (): void {
    $this->getJson('/internal-api/roles/list')
        ->assertUnauthorized();
});

it('sorts roles by name ascending', function (): void {
    $userModel = rolesGridAdmin();

    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c010', 'name' => 'Zulu Role', 'description' => 'Z', 'is_system' => false]);
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c011', 'name' => 'Alpha Role', 'description' => 'A', 'is_system' => false]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/roles/list?sort=name&direction=asc')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toEqual(['Alpha Role', 'Super Admin', 'Zulu Role']);
});

it('ignores invalid sort column', function (): void {
    $userModel = rolesGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/roles/list?sort=invalid&direction=asc')
        ->assertOk();
});

it('filters roles by search', function (): void {
    $userModel = rolesGridAdmin();

    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c020', 'name' => 'Editor', 'description' => 'Can edit', 'is_system' => false]);
    RoleModel::create(['id' => '550e8400-e29b-41d4-a716-44665544c021', 'name' => 'Viewer', 'description' => 'Can view', 'is_system' => false]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/roles/list?search=Editor')
        ->assertOk();

    $names = array_column($response->json('data'), 'name');
    expect($names)->toContain('Editor');
    expect($names)->not->toContain('Viewer');
});

it('includes is_system flag', function (): void {
    $userModel = rolesGridAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/roles/list')
        ->assertOk();

    /** @var list<array<string, mixed>> $data */
    $data = $response->json('data');
    /** @var array<string, mixed> $systemRole */
    $systemRole = collect($data)->firstWhere('name', 'Super Admin');
    expect($systemRole['is_system'])->toBeTrue();
});

it('paginates results', function (): void {
    $userModel = rolesGridAdmin();

    for ($i = 1; $i <= 20; $i++) {
        RoleModel::create([
            'id' => sprintf('550e8400-e29b-41d4-a716-4466554c%04d', $i),
            'name' => 'Role '.$i,
            'description' => 'Role '.$i,
            'is_system' => false,
        ]);
    }

    $this->actingAs($userModel)
        ->getJson('/internal-api/roles/list?page=2&per_page=15')
        ->assertOk()
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.total', 21);
});

it('returns permissions in response', function (): void {
    $userModel = rolesGridAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/roles/list')
        ->assertOk()
        ->assertJsonPath('permissions.can_read', true)
        ->assertJsonPath('permissions.can_update', true);
});
