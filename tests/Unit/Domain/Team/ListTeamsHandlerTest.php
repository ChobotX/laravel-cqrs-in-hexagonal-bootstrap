<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessDecision;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Team\TeamMembershipChecker;
use App\Domain\Team\Query\ListTeams\ListTeamsHandler;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use App\Domain\Team\Team;
use App\Domain\Team\TeamId;
use App\Domain\Team\TeamName;
use App\Domain\Team\TeamSlug;
use Tests\Helper\FakeTeamRepository;

function listTeamsAuthChecker(string $scope = 'all'): AuthorizationChecker
{
    return new readonly class($scope) implements AuthorizationChecker
    {
        public function __construct(private string $scope) {}

        public function can(string $userId, string $permission): bool
        {
            return true;
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
function listTeamsMembershipChecker(array $teamIds = []): TeamMembershipChecker
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

it('lists all teams for All scope', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $handler = new ListTeamsHandler($teamRepo, listTeamsAuthChecker('all'), listTeamsMembershipChecker());

    $result = $handler->handle(new ListTeamsQuery('user-1'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Engineering');
});

it('returns empty when no teams', function (): void {
    $teamRepo = new FakeTeamRepository;
    $handler = new ListTeamsHandler($teamRepo, listTeamsAuthChecker('all'), listTeamsMembershipChecker());

    $result = $handler->handle(new ListTeamsQuery('user-1'));

    expect($result)->toBe([]);
});

it('filters teams by membership for Team scope', function (): void {
    $visible = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440001'),
        new TeamName('My Team'),
        new TeamSlug('my-team'),
        'Visible',
        null,
    );

    $hidden = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440002'),
        new TeamName('Other Team'),
        new TeamSlug('other-team'),
        'Hidden',
        null,
    );

    $teamRepo = new FakeTeamRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $visible,
        '550e8400-e29b-41d4-a716-446655440002' => $hidden,
    ]);

    $handler = new ListTeamsHandler(
        $teamRepo,
        listTeamsAuthChecker('team'),
        listTeamsMembershipChecker(['550e8400-e29b-41d4-a716-446655440001']),
    );

    $result = $handler->handle(new ListTeamsQuery('user-1'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('My Team');
});

it('returns empty for Own scope', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $handler = new ListTeamsHandler($teamRepo, listTeamsAuthChecker('own'), listTeamsMembershipChecker());

    $result = $handler->handle(new ListTeamsQuery('user-1'));

    expect($result)->toBe([]);
});
