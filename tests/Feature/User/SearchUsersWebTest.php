<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function createSearchWebUser(string $id, string $name, string $email): UserModel
{
    return UserModel::create([
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'password' => Hash::make('password'),
    ]);
}

/** @return array{admin: UserModel} */
function setupSuperAdmin(): array
{
    $userModel = createSearchWebUser('550e8400-e29b-41d4-a716-446655440a00', 'Super Admin', 'superadmin@test.com');

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($userModel->id);

    return ['admin' => $userModel];
}

/** @return array{user: UserModel} */
function setupRegularUser(): array
{
    $userModel = createSearchWebUser('550e8400-e29b-41d4-a716-446655440b00', 'Regular User', 'regular@test.com');

    test()->seedSuperAdminRole();

    $role = test()->seedRoleWithPermissions(
        'Member',
        'Basic member',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );
    test()->assignRole($userModel->id, $role->id);

    return ['user' => $userModel];
}

it('superadmin can search and sees all users', function (): void {
    ['admin' => $admin] = setupSuperAdmin();

    createSearchWebUser('550e8400-e29b-41d4-a716-446655440c00', 'Other User', 'other@test.com');

    $response = $this->actingAs($admin)
        ->getJson('/internal-api/users/search?q=Other');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Other User');
});

it('regular user can search users', function (): void {
    ['user' => $user] = setupRegularUser();

    createSearchWebUser('550e8400-e29b-41d4-a716-446655440d00', 'Co Member', 'comember@test.com');

    $response = $this->actingAs($user)
        ->getJson('/internal-api/users/search?q=Co+Member');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Co Member');
});

it('excludes specified user ids', function (): void {
    ['admin' => $admin] = setupSuperAdmin();

    $userModel = createSearchWebUser('550e8400-e29b-41d4-a716-446655440f00', 'Exclude Test A', 'excludea@test.com');
    createSearchWebUser('550e8400-e29b-41d4-a716-446655440f01', 'Exclude Test B', 'excludeb@test.com');

    $response = $this->actingAs($admin)
        ->getJson('/internal-api/users/search?q=Exclude+Test&exclude[]='.$userModel->id);

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Exclude Test B');
});

it('requires authentication', function (): void {
    $this->getJson('/internal-api/users/search?q=test')
        ->assertUnauthorized();
});

it('returns initial results with empty query', function (): void {
    ['admin' => $admin] = setupSuperAdmin();

    createSearchWebUser('550e8400-e29b-41d4-a716-446655441100', 'Initial User', 'initial@test.com');

    $response = $this->actingAs($admin)
        ->getJson('/internal-api/users/search');

    $response->assertOk();
    $response->assertJsonFragment(['name' => 'Initial User']);
});

it('returns correct json structure', function (): void {
    ['admin' => $admin] = setupSuperAdmin();

    createSearchWebUser('550e8400-e29b-41d4-a716-446655441000', 'Struct User', 'struct@test.com');

    $response = $this->actingAs($admin)
        ->getJson('/internal-api/users/search?q=Struct');

    $response->assertOk();
    $response->assertJsonStructure(['data' => [['id', 'name', 'email']]]);
});
