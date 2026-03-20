<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Organization\EloquentTeamMemberRepository;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\Organization\TeamMemberMapper;
use App\Infrastructure\Eloquent\Organization\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;

function teamMemberRepo(): EloquentTeamMemberRepository
{
    return new EloquentTeamMemberRepository(new TeamMemberMapper);
}

function createTeamMemberTestOrg(string $id): void
{
    OrganizationModel::create([
        'id' => $id,
        'name' => 'Test Org',
        'slug' => 'tm-test-org-'.substr($id, -4),
        'description' => 'Test',
    ]);
}

function createTeamMemberTestTeam(string $id, string $orgId, ?string $parentId = null): void
{
    TeamModel::create([
        'id' => $id,
        'organization_id' => $orgId,
        'parent_team_id' => $parentId,
        'name' => 'Team '.substr($id, -4),
        'slug' => 'team-'.substr($id, -4),
        'description' => 'Test',
    ]);
}

function createTeamMemberTestUser(): UserModel
{
    return UserModel::factory()->create();
}

it('adds a member and verifies membership', function (): void {
    $userModel = createTeamMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440c00';
    createTeamMemberTestOrg($orgId);
    $teamId = '550e8400-e29b-41d4-a716-446655440d00';
    createTeamMemberTestTeam($teamId, $orgId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $teamId);

    expect($eloquentTeamMemberRepository->isMember($userModel->id, $teamId))->toBeTrue();
});

it('returns false for non-member', function (): void {
    $userModel = createTeamMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440c01';
    createTeamMemberTestOrg($orgId);
    $teamId = '550e8400-e29b-41d4-a716-446655440d01';
    createTeamMemberTestTeam($teamId, $orgId);

    $eloquentTeamMemberRepository = teamMemberRepo();

    expect($eloquentTeamMemberRepository->isMember($userModel->id, $teamId))->toBeFalse();
});

it('lists members of a team', function (): void {
    $userModel = createTeamMemberTestUser();
    $user2 = createTeamMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440c02';
    createTeamMemberTestOrg($orgId);
    $teamId = '550e8400-e29b-41d4-a716-446655440d02';
    createTeamMemberTestTeam($teamId, $orgId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $teamId);
    $eloquentTeamMemberRepository->add($user2->id, $teamId);

    $members = $eloquentTeamMemberRepository->listMembers($teamId);

    expect($members)->toHaveCount(2);
});

it('removes a member', function (): void {
    $userModel = createTeamMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440c03';
    createTeamMemberTestOrg($orgId);
    $teamId = '550e8400-e29b-41d4-a716-446655440d03';
    createTeamMemberTestTeam($teamId, $orgId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $teamId);
    $eloquentTeamMemberRepository->remove($userModel->id, $teamId);

    expect($eloquentTeamMemberRepository->isMember($userModel->id, $teamId))->toBeFalse();
});

it('returns member team ids with descendant expansion', function (): void {
    $userModel = createTeamMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440c04';
    createTeamMemberTestOrg($orgId);

    $parentId = '550e8400-e29b-41d4-a716-446655440d04';
    $childId = '550e8400-e29b-41d4-a716-446655440d05';
    $grandchildId = '550e8400-e29b-41d4-a716-446655440d06';

    createTeamMemberTestTeam($parentId, $orgId);
    createTeamMemberTestTeam($childId, $orgId, $parentId);
    createTeamMemberTestTeam($grandchildId, $orgId, $childId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $parentId);

    $ids = $eloquentTeamMemberRepository->memberTeamIds($userModel->id, $orgId);

    expect($ids)->toHaveCount(3)
        ->and($ids)->toContain($parentId, $childId, $grandchildId);
});

it('returns only direct team when member of leaf', function (): void {
    $userModel = createTeamMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440c05';
    createTeamMemberTestOrg($orgId);

    $parentId = '550e8400-e29b-41d4-a716-446655440d07';
    $childId = '550e8400-e29b-41d4-a716-446655440d08';

    createTeamMemberTestTeam($parentId, $orgId);
    createTeamMemberTestTeam($childId, $orgId, $parentId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $childId);

    $ids = $eloquentTeamMemberRepository->memberTeamIds($userModel->id, $orgId);

    expect($ids)->toHaveCount(1)
        ->and($ids)->toContain($childId);
});

it('removes all team memberships by user and organization', function (): void {
    $userModel = createTeamMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440c06';
    createTeamMemberTestOrg($orgId);

    $team1Id = '550e8400-e29b-41d4-a716-446655440d09';
    $team2Id = '550e8400-e29b-41d4-a716-446655440d0a';

    createTeamMemberTestTeam($team1Id, $orgId);
    createTeamMemberTestTeam($team2Id, $orgId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $team1Id);
    $eloquentTeamMemberRepository->add($userModel->id, $team2Id);

    $eloquentTeamMemberRepository->removeAllByUserAndOrganization($userModel->id, $orgId);

    expect($eloquentTeamMemberRepository->isMember($userModel->id, $team1Id))->toBeFalse()
        ->and($eloquentTeamMemberRepository->isMember($userModel->id, $team2Id))->toBeFalse();
});

it('returns empty member team ids when user has no teams', function (): void {
    $userModel = createTeamMemberTestUser();
    $orgId = '550e8400-e29b-41d4-a716-446655440c07';
    createTeamMemberTestOrg($orgId);
    createTeamMemberTestTeam('550e8400-e29b-41d4-a716-446655440d0b', $orgId);

    $eloquentTeamMemberRepository = teamMemberRepo();

    $ids = $eloquentTeamMemberRepository->memberTeamIds($userModel->id, $orgId);

    expect($ids)->toBe([]);
});
