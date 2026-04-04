<?php

declare(strict_types=1);

use App\Application\Authorization\AccessContext;
use App\Application\Authorization\ScopeTarget;
use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Query\ListTeamsQuery;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\Handler\Query\ListTeamsHandler;
use App\Domain\Team\ValueObject\TeamName;
use Tests\Helper\FakeTeamRepository;

it('lists all teams when visibleIds is null (All scope)', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $handler = new ListTeamsHandler($teamRepo);

    $listTeamsQuery = (new ListTeamsQuery)->withAccessContext(new AccessContext(AccessScope::All, null));
    $paginatedResult = $handler->handle($listTeamsQuery);

    expect($paginatedResult)->toBeInstanceOf(PaginatedResult::class)
        ->and($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->items[0]->name->value)->toBe('Engineering');
});

it('returns empty when no teams', function (): void {
    $teamRepo = new FakeTeamRepository;
    $handler = new ListTeamsHandler($teamRepo);

    $listTeamsQuery = (new ListTeamsQuery)->withAccessContext(new AccessContext(AccessScope::All, null));
    $paginatedResult = $handler->handle($listTeamsQuery);

    expect($paginatedResult->items)->toBeEmpty()
        ->and($paginatedResult->total)->toBe(0);
});

it('filters teams by visibleIds (Team scope)', function (): void {
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

    $handler = new ListTeamsHandler($teamRepo);

    $listTeamsQuery = (new ListTeamsQuery)->withAccessContext(
        new AccessContext(AccessScope::Team, ['550e8400-e29b-41d4-a716-446655440001']),
    );
    $paginatedResult = $handler->handle($listTeamsQuery);

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->items[0]->name->value)->toBe('My Team');
});

it('returns empty when visibleIds is empty (Own scope)', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $handler = new ListTeamsHandler($teamRepo);

    $listTeamsQuery = (new ListTeamsQuery)->withAccessContext(new AccessContext(AccessScope::Own, []));
    $paginatedResult = $handler->handle($listTeamsQuery);

    expect($paginatedResult->items)->toBeEmpty()
        ->and($paginatedResult->total)->toBe(0);
});

it('paginates teams when pagination is provided', function (): void {
    $teams = [];
    for ($i = 1; $i <= 3; $i++) {
        $id = sprintf('550e8400-e29b-41d4-a716-44665544%04d', $i);
        $teams[$id] = new Team(new TeamId($id), new TeamName('Team '.$i), new TeamSlug('team-'.$i), 'Desc', null);
    }

    $handler = new ListTeamsHandler(new FakeTeamRepository($teams));

    $listTeamsQuery = new ListTeamsQuery(pagination: new Pagination(1, 2))
        ->withAccessContext(new AccessContext(AccessScope::All, null));
    $paginatedResult = $handler->handle($listTeamsQuery);

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

    $handler = new ListTeamsHandler(new FakeTeamRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $visible,
        '550e8400-e29b-41d4-a716-446655440002' => $hidden,
    ]));

    $listTeamsQuery = new ListTeamsQuery(pagination: new Pagination(1, 10))
        ->withAccessContext(new AccessContext(AccessScope::Team, ['550e8400-e29b-41d4-a716-446655440001']));
    $paginatedResult = $handler->handle($listTeamsQuery);

    expect($paginatedResult->items)->toHaveCount(1)
        ->and($paginatedResult->total)->toBe(1);
});

it('returns empty paginated result for Own scope with pagination', function (): void {
    $handler = new ListTeamsHandler(new FakeTeamRepository);

    $listTeamsQuery = new ListTeamsQuery(pagination: new Pagination(1, 10))
        ->withAccessContext(new AccessContext(AccessScope::Own, []));
    $paginatedResult = $handler->handle($listTeamsQuery);

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

    $handler = new ListTeamsHandler(new FakeTeamRepository($teams));

    $listTeamsQuery = (new ListTeamsQuery)->withAccessContext(new AccessContext(AccessScope::All, null));
    $paginatedResult = $handler->handle($listTeamsQuery);

    expect($paginatedResult->items[0]->name->value)->toBe('Alpha')
        ->and($paginatedResult->items[1]->name->value)->toBe('Zebra');
});

it('supports withPagination immutable copy', function (): void {
    $query = new ListTeamsQuery;
    $listTeamsQuery = $query->withPagination(new Pagination(3, 5));

    expect($query->pagination())->toBeNull()
        ->and($listTeamsQuery->pagination())->toBeInstanceOf(Pagination::class)
        ->and($listTeamsQuery->pagination()?->page)->toBe(3);
});

it('supports withSorting immutable copy', function (): void {
    $query = new ListTeamsQuery;
    $listTeamsQuery = $query->withSorting([new Sorting('name', SortDirection::Desc)]);

    expect($query->sorting())->toBe([])
        ->and($listTeamsQuery->sorting())->toHaveCount(1)
        ->and($listTeamsQuery->sorting()[0]->column)->toBe('name')
        ->and($listTeamsQuery->sorting()[0]->direction)->toBe(SortDirection::Desc);
});

it('returns Team scope target', function (): void {
    $query = new ListTeamsQuery;

    expect($query->scopeTarget())->toBe(ScopeTarget::Team);
});

it('supports withAccessContext immutable copy', function (): void {
    $query = new ListTeamsQuery;
    $listTeamsQuery = $query->withAccessContext(new AccessContext(AccessScope::All, null));

    expect($query->accessContext())->toBeNull()
        ->and($listTeamsQuery->accessContext())->toBeInstanceOf(AccessContext::class)
        ->and($listTeamsQuery->accessContext()?->scope)->toBe(AccessScope::All);
});
