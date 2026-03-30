<?php

declare(strict_types=1);

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
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

    $paginatedResult = $handler->handle(new ListTeamsQuery('user-1'));

    expect($paginatedResult)->toBeInstanceOf(PaginatedResult::class)
        ->and($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->items[0]->name->value)->toBe('Engineering');
});

it('returns empty when no teams', function (): void {
    $teamRepo = new FakeTeamRepository;
    $handler = new ListTeamsHandler($teamRepo, listTeamsAuthChecker('all'), listTeamsMembershipChecker());

    $paginatedResult = $handler->handle(new ListTeamsQuery('user-1'));

    expect($paginatedResult->items)->toBeEmpty()
        ->and($paginatedResult->total)->toBe(0);
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

    $paginatedResult = $handler->handle(new ListTeamsQuery('user-1'));

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->items[0]->name->value)->toBe('My Team');
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

    $paginatedResult = $handler->handle(new ListTeamsQuery('user-1'));

    expect($paginatedResult->items)->toBeEmpty()
        ->and($paginatedResult->total)->toBe(0);
});

it('paginates teams when pagination is provided', function (): void {
    $teams = [];
    for ($i = 1; $i <= 3; $i++) {
        $id = sprintf('550e8400-e29b-41d4-a716-44665544%04d', $i);
        $teams[$id] = new Team(new TeamId($id), new TeamName('Team '.$i), new TeamSlug('team-'.$i), 'Desc', null);
    }

    $handler = new ListTeamsHandler(
        new FakeTeamRepository($teams),
        listTeamsAuthChecker('all'),
        listTeamsMembershipChecker(),
    );

    $paginatedResult = $handler->handle(new ListTeamsQuery('user-1', new Pagination(1, 2)));

    expect($paginatedResult->items)->toHaveCount(2)
        ->and($paginatedResult->total)->toBe(3)
        ->and($paginatedResult->pagination->page)->toBe(1);
});

it('paginates filtered teams for Team scope', function (): void {
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

    $handler = new ListTeamsHandler(
        new FakeTeamRepository([
            '550e8400-e29b-41d4-a716-446655440001' => $visible,
            '550e8400-e29b-41d4-a716-446655440002' => $hidden,
        ]),
        listTeamsAuthChecker('team'),
        listTeamsMembershipChecker(['550e8400-e29b-41d4-a716-446655440001']),
    );

    $paginatedResult = $handler->handle(new ListTeamsQuery('user-1', new Pagination(1, 10)));

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->total)->toBe(1);
});

it('returns empty paginated result for Own scope with pagination', function (): void {
    $handler = new ListTeamsHandler(
        new FakeTeamRepository,
        listTeamsAuthChecker('own'),
        listTeamsMembershipChecker(),
    );

    $paginatedResult = $handler->handle(new ListTeamsQuery('user-1', new Pagination(1, 10)));

    expect($paginatedResult->items)->toBeEmpty()
        ->and($paginatedResult->total)->toBe(0);
});

it('applies default sorting by name ascending', function (): void {
    $teams = [
        '550e8400-e29b-41d4-a716-446655440001' => new Team(
            new TeamId('550e8400-e29b-41d4-a716-446655440001'),
            new TeamName('Zebra'),
            new TeamSlug('zebra'),
            'Desc',
            null,
        ),
        '550e8400-e29b-41d4-a716-446655440002' => new Team(
            new TeamId('550e8400-e29b-41d4-a716-446655440002'),
            new TeamName('Alpha'),
            new TeamSlug('alpha'),
            'Desc',
            null,
        ),
    ];

    $handler = new ListTeamsHandler(
        new FakeTeamRepository($teams),
        listTeamsAuthChecker('all'),
        listTeamsMembershipChecker(),
    );

    $paginatedResult = $handler->handle(new ListTeamsQuery('user-1'));

    expect($paginatedResult->items[0]->name->value)->toBe('Alpha')
        ->and($paginatedResult->items[1]->name->value)->toBe('Zebra');
});

it('supports withPagination immutable copy', function (): void {
    $query = new ListTeamsQuery('user-1');
    $listTeamsQuery = $query->withPagination(new Pagination(3, 5));

    expect($query->pagination())->toBeNull()
        ->and($listTeamsQuery->pagination())->toBeInstanceOf(Pagination::class)
        ->and($listTeamsQuery->pagination())->not->toBeNull()
        ->and($listTeamsQuery->pagination()?->page)->toBe(3)
        ->and($listTeamsQuery->userId)->toBe('user-1');
});

it('supports withSorting immutable copy', function (): void {
    $query = new ListTeamsQuery('user-1');
    $listTeamsQuery = $query->withSorting([new Sorting('name', SortDirection::Desc)]);

    expect($query->sorting())->toBe([])
        ->and($listTeamsQuery->sorting())->toHaveCount(1)
        ->and($listTeamsQuery->sorting()[0]->column)->toBe('name')
        ->and($listTeamsQuery->sorting()[0]->direction)->toBe(SortDirection::Desc)
        ->and($listTeamsQuery->userId)->toBe('user-1');
});
