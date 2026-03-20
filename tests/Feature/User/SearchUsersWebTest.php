<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function createSearchWebOrg(string $id, string $slug): void
{
    OrganizationModel::create([
        'id' => $id,
        'name' => 'Org '.$slug,
        'slug' => $slug,
        'description' => 'Test',
    ]);
}

function createSearchWebUser(string $id, string $name, string $email): UserModel
{
    return UserModel::create([
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'password' => Hash::make('password'),
    ]);
}

function addSearchWebMember(string $userId, string $orgId): void
{
    OrganizationMemberModel::create([
        'user_id' => $userId,
        'organization_id' => $orgId,
        'joined_at' => now(),
    ]);
}

/** @return array{admin: UserModel} */
function setupSuperAdmin(): array
{
    createSearchWebOrg('00000000-0000-0000-0000-000000000001', 'default');

    $userModel = createSearchWebUser('550e8400-e29b-41d4-a716-446655440a00', 'Super Admin', 'superadmin@test.com');
    addSearchWebMember($userModel->id, '00000000-0000-0000-0000-000000000001');

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($userModel->id);

    return ['admin' => $userModel];
}

/** @return array{user: UserModel, orgId: string} */
function setupRegularUser(): array
{
    createSearchWebOrg('00000000-0000-0000-0000-000000000001', 'default');
    $orgId = '00000000-0000-0000-0000-000000000002';
    createSearchWebOrg($orgId, 'user-org');

    $userModel = createSearchWebUser('550e8400-e29b-41d4-a716-446655440b00', 'Regular User', 'regular@test.com');
    addSearchWebMember($userModel->id, '00000000-0000-0000-0000-000000000001');
    addSearchWebMember($userModel->id, $orgId);

    test()->seedSuperAdminRole();

    $role = test()->seedRoleWithPermissions(
        '00000000-0000-0000-0000-000000000001',
        'Member',
        'Basic member',
        ['users.list.read' => 'all', 'users.roles.read' => 'all'],
    );
    test()->assignRole($userModel->id, $role->id, '00000000-0000-0000-0000-000000000001');

    return ['user' => $userModel, 'orgId' => $orgId];
}

it('superadmin can search and sees users from any organization', function (): void {
    ['admin' => $admin] = setupSuperAdmin();

    createSearchWebOrg('00000000-0000-0000-0000-000000000003', 'other-org');
    $userModel = createSearchWebUser('550e8400-e29b-41d4-a716-446655440c00', 'Other Org User', 'other@test.com');
    addSearchWebMember($userModel->id, '00000000-0000-0000-0000-000000000003');

    $response = $this->actingAs($admin)
        ->getJson('/internal-api/users/search?q=Other');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Other Org User');
});

it('regular user only sees co-members from same organizations', function (): void {
    ['user' => $user, 'orgId' => $orgId] = setupRegularUser();

    $userModel = createSearchWebUser('550e8400-e29b-41d4-a716-446655440d00', 'Co Member', 'comember@test.com');
    addSearchWebMember($userModel->id, $orgId);

    $response = $this->actingAs($user)
        ->getJson('/internal-api/users/search?q=Co+Member');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.name', 'Co Member');
});

it('regular user cannot see users from other organizations', function (): void {
    ['user' => $user] = setupRegularUser();

    createSearchWebOrg('00000000-0000-0000-0000-000000000004', 'alien-org');
    $userModel = createSearchWebUser('550e8400-e29b-41d4-a716-446655440e00', 'Alien User', 'alien@test.com');
    addSearchWebMember($userModel->id, '00000000-0000-0000-0000-000000000004');

    $response = $this->actingAs($user)
        ->getJson('/internal-api/users/search?q=Alien');

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
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
