<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\UserPermissionOverrideModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use App\Infrastructure\Eloquent\Organization\EloquentOrganizationMemberRepository;
use App\Infrastructure\Eloquent\Organization\OrganizationMemberMapper;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Support\Str;

function memberRepo(): EloquentOrganizationMemberRepository
{
    return new EloquentOrganizationMemberRepository(new OrganizationMemberMapper);
}

function createOrgMemberTestOrg(string $id): void
{
    OrganizationModel::create([
        'id' => $id,
        'name' => 'Test Org',
        'slug' => 'test-org-'.substr($id, -4),
        'description' => 'Test',
    ]);
}

function createOrgMemberTestUser(): UserModel
{
    return UserModel::factory()->create();
}

it('adds a member and verifies membership', function (): void {
    $userModel = createOrgMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440900';
    createOrgMemberTestOrg($orgId);

    $eloquentOrganizationMemberRepository = memberRepo();
    $eloquentOrganizationMemberRepository->add($userModel->id, $orgId);

    expect($eloquentOrganizationMemberRepository->isMember($userModel->id, $orgId))->toBeTrue();
});

it('returns false for non-member', function (): void {
    $userModel = createOrgMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440901';
    createOrgMemberTestOrg($orgId);

    $eloquentOrganizationMemberRepository = memberRepo();

    expect($eloquentOrganizationMemberRepository->isMember($userModel->id, $orgId))->toBeFalse();
});

it('returns member organization ids', function (): void {
    $userModel = createOrgMemberTestUser();
    $orgId1 = '550e8400-e29b-41d4-a716-446655440902';
    $orgId2 = '550e8400-e29b-41d4-a716-446655440903';
    createOrgMemberTestOrg($orgId1);
    createOrgMemberTestOrg($orgId2);

    $eloquentOrganizationMemberRepository = memberRepo();
    $eloquentOrganizationMemberRepository->add($userModel->id, $orgId1);
    $eloquentOrganizationMemberRepository->add($userModel->id, $orgId2);

    $ids = $eloquentOrganizationMemberRepository->memberOrganizationIds($userModel->id);

    expect($ids)->toHaveCount(2)
        ->and($ids)->toContain($orgId1, $orgId2);
});

it('lists members of an organization', function (): void {
    $userModel = createOrgMemberTestUser();
    $user2 = createOrgMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440904';
    createOrgMemberTestOrg($orgId);

    $eloquentOrganizationMemberRepository = memberRepo();
    $eloquentOrganizationMemberRepository->add($userModel->id, $orgId);
    $eloquentOrganizationMemberRepository->add($user2->id, $orgId);

    $members = $eloquentOrganizationMemberRepository->listMembers($orgId);

    expect($members)->toHaveCount(2);
});

it('removes a member and cascades role/override deletion', function (): void {
    $userModel = createOrgMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440905';
    createOrgMemberTestOrg($orgId);

    $eloquentOrganizationMemberRepository = memberRepo();
    $eloquentOrganizationMemberRepository->add($userModel->id, $orgId);

    $roleModel = RoleModel::create([
        'id' => Str::uuid()->toString(),
        'organization_id' => $orgId,
        'name' => 'Test Role',
        'description' => 'Test',
        'is_system' => false,
    ]);

    UserRoleModel::create([
        'id' => Str::uuid()->toString(),
        'user_id' => $userModel->id,
        'role_id' => $roleModel->id,
        'organization_id' => $orgId,
    ]);

    UserPermissionOverrideModel::create([
        'id' => Str::uuid()->toString(),
        'user_id' => $userModel->id,
        'organization_id' => $orgId,
        'module' => 'users',
        'feature' => 'list',
        'action' => 'read',
        'type' => 'grant',
        'scope' => 'all',
    ]);

    $eloquentOrganizationMemberRepository->remove($userModel->id, $orgId);

    expect($eloquentOrganizationMemberRepository->isMember($userModel->id, $orgId))->toBeFalse();
    expect(UserRoleModel::where('user_id', $userModel->id)->where('organization_id', $orgId)->count())->toBe(0);
    expect(UserPermissionOverrideModel::where('user_id', $userModel->id)->where('organization_id', $orgId)->count())->toBe(0);
});
