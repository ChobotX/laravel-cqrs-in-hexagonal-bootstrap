<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RecordShareModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Facades\Hash;

function sharingAdmin(): UserModel
{
    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441001',
        'name' => 'Sharing Admin',
        'email' => 'sharing-admin@test.com',
        'password' => Hash::make('password'),
    ]);

    test()->seedSuperAdminRole();
    test()->assignSuperAdmin($user->id);

    return $user;
}

function sharingGrantee(): UserModel
{
    return UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441002',
        'name' => 'Sharing Grantee',
        'email' => 'sharing-grantee@test.com',
        'password' => Hash::make('password'),
    ]);
}

function sharingNoPermUser(): UserModel
{
    return UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441003',
        'name' => 'No Perm User',
        'email' => 'sharing-noperm@test.com',
        'password' => Hash::make('password'),
    ]);
}

// --- GET /internal-api/shares/{resourceType}/{resourceId} ---

it('returns 401 when unauthenticated for get shares', function (): void {
    $this->getJson('/internal-api/shares/entry/some-resource-id')
        ->assertUnauthorized();
});

it('returns 400 when resource type is not supported for get shares', function (): void {
    $userModel = sharingAdmin();

    $this->actingAs($userModel)
        ->getJson('/internal-api/shares/unsupported/some-resource-id')
        ->assertBadRequest();
});

it('returns 403 when user lacks view permission for get shares', function (): void {
    $role = test()->seedRoleWithPermissions(
        'Shares View Denied',
        'No sharing permissions',
        [],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441010',
        'name' => 'No View',
        'email' => 'sharing-noview@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->assignRole($user->id, $role->id);

    $this->actingAs($user)
        ->getJson('/internal-api/shares/entry/some-resource-id')
        ->assertForbidden();
});

it('returns shares with grantee details on success', function (): void {
    $userModel = sharingAdmin();
    $grantee = sharingGrantee();

    RecordShareModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441020',
        'grantee_user_id' => $grantee->id,
        'resource_type' => 'entry',
        'resource_id' => '550e8400-e29b-41d4-a716-446655441099',
        'action' => 'read',
        'grantor_user_id' => $userModel->id,
    ]);

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/shares/entry/550e8400-e29b-41d4-a716-446655441099')
        ->assertOk()
        ->assertJsonStructure(['data' => [['grantee_user_id', 'grantee_name', 'grantee_email', 'grantor_user_id']]]);

    /** @var list<array{grantee_user_id: string}> $data */
    $data = $response->json('data');
    expect($data)->toHaveCount(1)
        ->and($data[0]['grantee_user_id'])->toBe($grantee->id);
});

it('returns empty data when no shares exist', function (): void {
    $userModel = sharingAdmin();

    $response = $this->actingAs($userModel)
        ->getJson('/internal-api/shares/entry/550e8400-e29b-41d4-a716-446655441098')
        ->assertOk();

    expect($response->json('data'))->toBe([]);
});

// --- POST /internal-api/shares/{resourceType}/{resourceId} ---

it('returns 401 when unauthenticated for share entity', function (): void {
    $this->postJson('/internal-api/shares/entry/some-resource-id', [
        'grantee_user_id' => '550e8400-e29b-41d4-a716-446655441002',
    ])->assertUnauthorized();
});

it('returns 400 when resource type is not supported for share entity', function (): void {
    $userModel = sharingAdmin();

    $this->actingAs($userModel)
        ->postJson('/internal-api/shares/unsupported/some-resource-id', [
            'grantee_user_id' => '550e8400-e29b-41d4-a716-446655441002',
        ])->assertBadRequest();
});

it('returns 403 when user lacks share permission', function (): void {
    $role = test()->seedRoleWithPermissions(
        'Shares Store Denied',
        'No share permission',
        [],
    );

    $user = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441030',
        'name' => 'No Share',
        'email' => 'sharing-nostore@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->assignRole($user->id, $role->id);

    $this->actingAs($user)
        ->postJson('/internal-api/shares/entry/some-resource-id', [
            'grantee_user_id' => '550e8400-e29b-41d4-a716-446655441002',
        ])->assertForbidden();
});

it('returns 400 when trying to share with yourself', function (): void {
    $userModel = sharingAdmin();

    $this->actingAs($userModel)
        ->postJson('/internal-api/shares/entry/some-resource-id', [
            'grantee_user_id' => $userModel->id,
        ])->assertBadRequest();
});

it('creates a share and returns 201 on success', function (): void {
    $userModel = sharingAdmin();
    $grantee = sharingGrantee();

    $response = $this->actingAs($userModel)
        ->postJson('/internal-api/shares/entry/550e8400-e29b-41d4-a716-446655441097', [
            'grantee_user_id' => $grantee->id,
        ])->assertCreated()
        ->assertJsonStructure(['data' => ['grantee_user_id', 'resource_type', 'resource_id']]);

    expect($response->json('data.grantee_user_id'))->toBe($grantee->id)
        ->and($response->json('data.resource_type'))->toBe('entry')
        ->and($response->json('data.resource_id'))->toBe('550e8400-e29b-41d4-a716-446655441097');
});

// --- DELETE /internal-api/shares/{resourceType}/{resourceId}/{granteeUserId} ---

it('returns 401 when unauthenticated for revoke share', function (): void {
    $this->deleteJson('/internal-api/shares/entry/some-resource-id/some-grantee-id')
        ->assertUnauthorized();
});

it('returns 400 when resource type is not supported for revoke share', function (): void {
    $userModel = sharingAdmin();

    $this->actingAs($userModel)
        ->deleteJson('/internal-api/shares/unsupported/some-resource-id/some-grantee-id')
        ->assertBadRequest();
});

it('returns 404 when share does not exist for revoke', function (): void {
    $userModel = sharingAdmin();

    $this->actingAs($userModel)
        ->deleteJson('/internal-api/shares/entry/550e8400-e29b-41d4-a716-446655441093/550e8400-e29b-41d4-a716-446655441092')
        ->assertNotFound();
});

it('returns 403 when user is neither grantor nor has update permission', function (): void {
    $userModel = sharingAdmin();
    $grantee = sharingGrantee();

    RecordShareModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441040',
        'grantee_user_id' => $grantee->id,
        'resource_type' => 'entry',
        'resource_id' => '550e8400-e29b-41d4-a716-446655441096',
        'action' => 'read',
        'grantor_user_id' => $userModel->id,
    ]);

    $role = test()->seedRoleWithPermissions(
        'Revoke Denied',
        'No revoke permission',
        [],
    );

    $other = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441050',
        'name' => 'Other User',
        'email' => 'sharing-other@test.com',
        'password' => Hash::make('password'),
    ]);
    test()->assignRole($other->id, $role->id);

    $this->actingAs($other)
        ->deleteJson('/internal-api/shares/entry/550e8400-e29b-41d4-a716-446655441096/'.$grantee->id)
        ->assertForbidden();
});

it('grantor can revoke their own share and returns 204', function (): void {
    $userModel = sharingAdmin();
    $grantee = sharingGrantee();

    RecordShareModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441060',
        'grantee_user_id' => $grantee->id,
        'resource_type' => 'entry',
        'resource_id' => '550e8400-e29b-41d4-a716-446655441095',
        'action' => 'read',
        'grantor_user_id' => $userModel->id,
    ]);

    $this->actingAs($userModel)
        ->deleteJson('/internal-api/shares/entry/550e8400-e29b-41d4-a716-446655441095/'.$grantee->id)
        ->assertNoContent();
});

it('user with update permission can revoke a share', function (): void {
    $userModel = sharingAdmin();
    $grantee = sharingGrantee();

    $anotherUser = UserModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441070',
        'name' => 'Another Grantor',
        'email' => 'sharing-another-grantor@test.com',
        'password' => Hash::make('password'),
    ]);

    RecordShareModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655441071',
        'grantee_user_id' => $grantee->id,
        'resource_type' => 'entry',
        'resource_id' => '550e8400-e29b-41d4-a716-446655441094',
        'action' => 'read',
        'grantor_user_id' => $anotherUser->id,
    ]);

    // admin has super-admin (has all permissions, including registry.entries.update)
    $this->actingAs($userModel)
        ->deleteJson('/internal-api/shares/entry/550e8400-e29b-41d4-a716-446655441094/'.$grantee->id)
        ->assertNoContent();
});
