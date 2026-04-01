<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Team\EloquentTeamMemberRepository;
use App\Infrastructure\Eloquent\Team\TeamMemberMapper;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;

function teamMemberRepo(): EloquentTeamMemberRepository
{
    return new EloquentTeamMemberRepository(new TeamMemberMapper);
}

function createTeamMemberTestTeam(string $id, ?string $parentId = null): void
{
    TeamModel::create([
        'id' => $id,
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
    $teamId = '550e8400-e29b-41d4-a716-446655440d00';
    createTeamMemberTestTeam($teamId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $teamId);

    expect($eloquentTeamMemberRepository->isMember($userModel->id, $teamId))->toBeTrue();
});

it('returns false for non-member', function (): void {
    $userModel = createTeamMemberTestUser();
    $teamId = '550e8400-e29b-41d4-a716-446655440d01';
    createTeamMemberTestTeam($teamId);

    $eloquentTeamMemberRepository = teamMemberRepo();

    expect($eloquentTeamMemberRepository->isMember($userModel->id, $teamId))->toBeFalse();
});

it('lists members of a team', function (): void {
    $userModel = createTeamMemberTestUser();
    $user2 = createTeamMemberTestUser();
    $teamId = '550e8400-e29b-41d4-a716-446655440d02';
    createTeamMemberTestTeam($teamId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $teamId);
    $eloquentTeamMemberRepository->add($user2->id, $teamId);

    $members = $eloquentTeamMemberRepository->listMembers($teamId);

    expect($members)->toHaveCount(2);
});

it('removes a member', function (): void {
    $userModel = createTeamMemberTestUser();
    $teamId = '550e8400-e29b-41d4-a716-446655440d03';
    createTeamMemberTestTeam($teamId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $teamId);
    $eloquentTeamMemberRepository->remove($userModel->id, $teamId);

    expect($eloquentTeamMemberRepository->isMember($userModel->id, $teamId))->toBeFalse();
});

it('returns member team ids with descendant expansion', function (): void {
    $userModel = createTeamMemberTestUser();

    $parentId = '550e8400-e29b-41d4-a716-446655440d04';
    $childId = '550e8400-e29b-41d4-a716-446655440d05';
    $grandchildId = '550e8400-e29b-41d4-a716-446655440d06';

    createTeamMemberTestTeam($parentId);
    createTeamMemberTestTeam($childId, $parentId);
    createTeamMemberTestTeam($grandchildId, $childId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $parentId);

    $ids = $eloquentTeamMemberRepository->memberTeamIds($userModel->id);

    expect($ids)->toHaveCount(3)
        ->and($ids)->toContain($parentId, $childId, $grandchildId);
});

it('returns only direct team when member of leaf', function (): void {
    $userModel = createTeamMemberTestUser();

    $parentId = '550e8400-e29b-41d4-a716-446655440d07';
    $childId = '550e8400-e29b-41d4-a716-446655440d08';

    createTeamMemberTestTeam($parentId);
    createTeamMemberTestTeam($childId, $parentId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $childId);

    $ids = $eloquentTeamMemberRepository->memberTeamIds($userModel->id);

    expect($ids)->toHaveCount(1)
        ->and($ids)->toContain($childId);
});

it('removes all team memberships by user', function (): void {
    $userModel = createTeamMemberTestUser();

    $team1Id = '550e8400-e29b-41d4-a716-446655440d09';
    $team2Id = '550e8400-e29b-41d4-a716-446655440d0a';

    createTeamMemberTestTeam($team1Id);
    createTeamMemberTestTeam($team2Id);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $team1Id);
    $eloquentTeamMemberRepository->add($userModel->id, $team2Id);

    $eloquentTeamMemberRepository->removeAllByUser($userModel->id);

    expect($eloquentTeamMemberRepository->isMember($userModel->id, $team1Id))->toBeFalse()
        ->and($eloquentTeamMemberRepository->isMember($userModel->id, $team2Id))->toBeFalse();
});

it('returns visible user ids from teams and descendants in single query', function (): void {
    $userModel = createTeamMemberTestUser();
    $user2 = createTeamMemberTestUser();
    $user3 = createTeamMemberTestUser();
    $outsider = createTeamMemberTestUser();

    $parentId = '550e8400-e29b-41d4-a716-446655440d10';
    $childId = '550e8400-e29b-41d4-a716-446655440d11';
    $unrelatedId = '550e8400-e29b-41d4-a716-446655440d12';

    createTeamMemberTestTeam($parentId);
    createTeamMemberTestTeam($childId, $parentId);
    createTeamMemberTestTeam($unrelatedId);

    $eloquentTeamMemberRepository = teamMemberRepo();
    $eloquentTeamMemberRepository->add($userModel->id, $parentId);
    $eloquentTeamMemberRepository->add($user2->id, $parentId);
    $eloquentTeamMemberRepository->add($user3->id, $childId);
    $eloquentTeamMemberRepository->add($outsider->id, $unrelatedId);

    $ids = $eloquentTeamMemberRepository->visibleUserIds($userModel->id);

    expect($ids)->toContain($userModel->id, $user2->id, $user3->id)
        ->and($ids)->not->toContain($outsider->id);
});

it('returns only self when user has no teams', function (): void {
    $userModel = createTeamMemberTestUser();

    $eloquentTeamMemberRepository = teamMemberRepo();

    $ids = $eloquentTeamMemberRepository->visibleUserIds($userModel->id);

    expect($ids)->toBe([$userModel->id]);
});

it('returns empty member team ids when user has no teams', function (): void {
    $userModel = createTeamMemberTestUser();
    createTeamMemberTestTeam('550e8400-e29b-41d4-a716-446655440d0b');

    $eloquentTeamMemberRepository = teamMemberRepo();

    $ids = $eloquentTeamMemberRepository->memberTeamIds($userModel->id);

    expect($ids)->toBe([]);
});
