<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessDecision;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Team\TeamMembershipChecker;
use App\Domain\Team\Query\GetTeamTree\GetTeamTreeHandler;
use App\Domain\Team\Query\GetTeamTree\GetTeamTreeQuery;
use App\Domain\Team\Team;
use App\Domain\Team\TeamId;
use App\Domain\Team\TeamName;
use App\Domain\Team\TeamSlug;
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

function makeAuthorizationChecker(string $scope = 'all', bool $canRead = true): AuthorizationChecker
{
    return new readonly class($scope, $canRead) implements AuthorizationChecker
    {
        public function __construct(
            private string $scope,
            private bool $canRead,
        ) {}

        public function can(string $userId, string $permission): bool
        {
            return $this->canRead;
        }

        public function canWithScope(string $userId, string $permission): AccessDecision
        {
            return new readonly class($this->scope) implements AccessDecision
            {
                public function __construct(private string $scope) {}

                public function granted(): bool
                {
                    return true;
                }

                public function scope(): string
                {
                    return $this->scope;
                }
            };
        }

        /** @return list<string> */
        public function accessibleResourceIds(string $userId, string $resourceType, string $action): array
        {
            return [];
        }
    };
}

/** @param list<string> $teamIds */
function makeMembershipChecker(array $teamIds = []): TeamMembershipChecker
{
    return new readonly class($teamIds) implements TeamMembershipChecker
    {
        /** @param list<string> $teamIds */
        public function __construct(private array $teamIds) {}

        public function isTeamMember(string $userId, string $teamId): bool
        {
            return in_array($teamId, $this->teamIds, true);
        }

        /** @return list<string> */
        public function memberTeamIds(string $userId): array
        {
            return $this->teamIds;
        }

        /** @return list<string> */
        public function visibleUserIds(string $userId): array
        {
            return [];
        }
    };
}

$companyId = '550e8400-e29b-41d4-a716-446655440001';
$engineeringId = '550e8400-e29b-41d4-a716-446655440002';
$backendId = '550e8400-e29b-41d4-a716-446655440003';
$userId = '550e8400-e29b-41d4-a716-446655440099';

it('returns all teams with members for All scope', function () use ($companyId, $engineeringId, $userId): void {
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

    $handler = new GetTeamTreeHandler(
        $teamRepo,
        $memberRepo,
        makeAuthorizationChecker('all'),
        makeMembershipChecker(),
    );

    $result = $handler->handle(new GetTeamTreeQuery($userId));

    expect($result)->toHaveCount(2)
        ->and($result[0]->team->name->value)->toBe('Company')
        ->and($result[0]->members)->toHaveCount(1)
        ->and($result[0]->members[0]->userName)->toBe('Alice')
        ->and($result[1]->team->name->value)->toBe('Engineering')
        ->and($result[1]->members)->toHaveCount(0);
});

it('returns only accessible teams for Team scope', function () use ($companyId, $engineeringId, $backendId, $userId): void {
    $team = makeTeam($companyId, 'Company');
    $engineering = makeTeam($engineeringId, 'Engineering', $companyId);
    $backend = makeTeam($backendId, 'Backend', $engineeringId);

    $teamRepo = new FakeTeamRepository([
        $companyId => $team,
        $engineeringId => $engineering,
        $backendId => $backend,
    ]);
    $memberRepo = new FakeTeamMemberRepository;

    $handler = new GetTeamTreeHandler(
        $teamRepo,
        $memberRepo,
        makeAuthorizationChecker('team'),
        makeMembershipChecker([$engineeringId, $backendId]),
    );

    $result = $handler->handle(new GetTeamTreeQuery($userId));

    expect($result)->toHaveCount(2);

    $names = array_map(fn (App\Domain\Team\Query\GetTeamTree\TeamTreeNode $teamTreeNode): string => $teamTreeNode->team->name->value, $result);
    expect($names)->toContain('Engineering', 'Backend');
    expect($names)->not()->toContain('Company');
});

it('returns empty for Own scope', function () use ($companyId, $userId): void {
    $team = makeTeam($companyId, 'Company');

    $teamRepo = new FakeTeamRepository([$companyId => $team]);
    $memberRepo = new FakeTeamMemberRepository;

    $handler = new GetTeamTreeHandler(
        $teamRepo,
        $memberRepo,
        makeAuthorizationChecker('own'),
        makeMembershipChecker(),
    );

    $result = $handler->handle(new GetTeamTreeQuery($userId));

    expect($result)->toBe([]);
});

it('returns empty when repository is empty', function () use ($userId): void {
    $handler = new GetTeamTreeHandler(
        new FakeTeamRepository,
        new FakeTeamMemberRepository,
        makeAuthorizationChecker('all'),
        makeMembershipChecker(),
    );

    $result = $handler->handle(new GetTeamTreeQuery($userId));

    expect($result)->toBe([]);
});

it('includes members for each team node', function () use ($companyId, $userId): void {
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

    $handler = new GetTeamTreeHandler(
        $teamRepo,
        $memberRepo,
        makeAuthorizationChecker('all'),
        makeMembershipChecker(),
    );

    $result = $handler->handle(new GetTeamTreeQuery($userId));

    expect($result)->toHaveCount(1)
        ->and($result[0]->members)->toHaveCount(2);

    $memberNames = array_map(fn (App\Domain\Team\TeamMember $teamMember): string => $teamMember->userName, $result[0]->members);
    expect($memberNames)->toContain('Alice', 'Bob');
});

it('returns teams with empty member lists when teams have no members', function () use ($companyId, $userId): void {
    $team = makeTeam($companyId, 'Company');

    $teamRepo = new FakeTeamRepository([$companyId => $team]);
    $memberRepo = new FakeTeamMemberRepository;

    $handler = new GetTeamTreeHandler(
        $teamRepo,
        $memberRepo,
        makeAuthorizationChecker('all'),
        makeMembershipChecker(),
    );

    $result = $handler->handle(new GetTeamTreeQuery($userId));

    expect($result)->toHaveCount(1)
        ->and($result[0]->members)->toBe([]);
});
