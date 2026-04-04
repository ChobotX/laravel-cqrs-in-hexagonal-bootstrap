<?php

declare(strict_types=1);

use App\Application\Authorization\AccessContext;
use App\Contract\Authorization\AccessScope;
use App\Domain\Team\Contract\Query\GetTeamTreeQuery;
use App\Domain\Team\Contract\Query\TeamTreeNode;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamMember;
use App\Domain\Team\Contract\TeamSlug;
use App\Domain\Team\Handler\Query\GetTeamTreeHandler;
use App\Domain\Team\TeamName;
use Tests\Helper\FakeTeamMemberRepository;
use Tests\Helper\FakeTeamRepository;

function makeTeam(string $id, string $name, ?string $parentId = null): Team
{
    return new Team(
        new TeamId($id),
        new TeamName($name),
        new TeamSlug(mb_strtolower($name)),
        'Description',
        $parentId !== null ? new TeamId($parentId) : null,
    );
}

$companyId = '550e8400-e29b-41d4-a716-446655440001';
$engineeringId = '550e8400-e29b-41d4-a716-446655440002';
$backendId = '550e8400-e29b-41d4-a716-446655440003';

it('returns all teams with members for All scope', function () use ($companyId, $engineeringId): void {
    $team = makeTeam($companyId, 'Company');
    $engineering = makeTeam($engineeringId, 'Engineering', $companyId);

    $teamRepo = new FakeTeamRepository([
        $companyId => $team,
        $engineeringId => $engineering,
    ]);
    $memberRepo = new FakeTeamMemberRepository(
        memberships: ['user-a' => [$companyId]],
        userInfo: ['user-a' => ['name' => 'Alice', 'email' => 'alice@test.com']],
    );

    $handler = new GetTeamTreeHandler($teamRepo, $memberRepo);

    $getTeamTreeQuery = (new GetTeamTreeQuery)->withAccessContext(new AccessContext(AccessScope::All, null));
    $result = $handler->handle($getTeamTreeQuery);

    expect($result)->toHaveCount(2)
        ->and($result[0]->team->name->value)->toBe('Company')
        ->and($result[0]->members)->toHaveCount(1)
        ->and($result[0]->members[0]->userName)->toBe('Alice')
        ->and($result[1]->team->name->value)->toBe('Engineering')
        ->and($result[1]->members)->toHaveCount(0);
});

it('returns only accessible teams for Team scope', function () use ($companyId, $engineeringId, $backendId): void {
    $team = makeTeam($companyId, 'Company');
    $engineering = makeTeam($engineeringId, 'Engineering', $companyId);
    $backend = makeTeam($backendId, 'Backend', $engineeringId);

    $teamRepo = new FakeTeamRepository([
        $companyId => $team,
        $engineeringId => $engineering,
        $backendId => $backend,
    ]);
    $memberRepo = new FakeTeamMemberRepository;

    $handler = new GetTeamTreeHandler($teamRepo, $memberRepo);

    $getTeamTreeQuery = (new GetTeamTreeQuery)->withAccessContext(new AccessContext(AccessScope::Team, [$engineeringId, $backendId]));
    $result = $handler->handle($getTeamTreeQuery);

    expect($result)->toHaveCount(2);

    $names = array_map(fn (TeamTreeNode $teamTreeNode): string => $teamTreeNode->team->name->value, $result);
    expect($names)->toContain('Engineering', 'Backend');
    expect($names)->not()->toContain('Company');
});

it('returns empty for Own scope', function () use ($companyId): void {
    $team = makeTeam($companyId, 'Company');

    $teamRepo = new FakeTeamRepository([$companyId => $team]);
    $memberRepo = new FakeTeamMemberRepository;

    $handler = new GetTeamTreeHandler($teamRepo, $memberRepo);

    $getTeamTreeQuery = (new GetTeamTreeQuery)->withAccessContext(new AccessContext(AccessScope::Own, []));
    $result = $handler->handle($getTeamTreeQuery);

    expect($result)->toBe([]);
});

it('returns empty when repository is empty', function (): void {
    $handler = new GetTeamTreeHandler(new FakeTeamRepository, new FakeTeamMemberRepository);

    $getTeamTreeQuery = (new GetTeamTreeQuery)->withAccessContext(new AccessContext(AccessScope::All, null));
    $result = $handler->handle($getTeamTreeQuery);

    expect($result)->toBe([]);
});

it('includes members for each team node', function () use ($companyId): void {
    $team = makeTeam($companyId, 'Company');

    $teamRepo = new FakeTeamRepository([$companyId => $team]);
    $memberRepo = new FakeTeamMemberRepository(
        memberships: [
            'user-a' => [$companyId],
            'user-b' => [$companyId],
        ],
        userInfo: [
            'user-a' => ['name' => 'Alice', 'email' => 'alice@test.com'],
            'user-b' => ['name' => 'Bob', 'email' => 'bob@test.com'],
        ],
    );

    $handler = new GetTeamTreeHandler($teamRepo, $memberRepo);

    $getTeamTreeQuery = (new GetTeamTreeQuery)->withAccessContext(new AccessContext(AccessScope::All, null));
    $result = $handler->handle($getTeamTreeQuery);

    expect($result)->toHaveCount(1)
        ->and($result[0]->members)->toHaveCount(2);

    $memberNames = array_map(fn (TeamMember $teamMember): string => $teamMember->userName, $result[0]->members);
    expect($memberNames)->toContain('Alice', 'Bob');
});

it('returns teams with empty member lists when teams have no members', function () use ($companyId): void {
    $team = makeTeam($companyId, 'Company');

    $teamRepo = new FakeTeamRepository([$companyId => $team]);
    $memberRepo = new FakeTeamMemberRepository;

    $handler = new GetTeamTreeHandler($teamRepo, $memberRepo);

    $getTeamTreeQuery = (new GetTeamTreeQuery)->withAccessContext(new AccessContext(AccessScope::All, null));
    $result = $handler->handle($getTeamTreeQuery);

    expect($result)->toHaveCount(1)
        ->and($result[0]->members)->toBe([]);
});

it('returns Team scope target', function (): void {
    $query = new GetTeamTreeQuery;

    expect($query->scopeTarget())->toBe(App\Application\Authorization\ScopeTarget::Team);
});

it('supports withAccessContext immutable copy', function (): void {
    $query = new GetTeamTreeQuery;
    $getTeamTreeQuery = $query->withAccessContext(new AccessContext(AccessScope::Team, ['team-1']));

    $accessContext = $getTeamTreeQuery->accessContext();

    expect($query->accessContext())->toBeNull()
        ->and($accessContext)->not->toBeNull();

    assert($accessContext instanceof AccessContext);

    expect($accessContext->scope)->toBe(AccessScope::Team)
        ->and($accessContext->visibleIds)->toBe(['team-1']);
});
