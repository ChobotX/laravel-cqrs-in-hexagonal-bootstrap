<?php

declare(strict_types=1);

use App\Domain\Team\Contract\Query\SearchTeams\SearchTeamsQuery;
use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamSlug;
use App\Domain\Team\Query\SearchTeams\SearchTeamsHandler;
use App\Domain\Team\TeamName;
use Tests\Helper\FakeTeamRepository;

function searchTeamsRepository(): FakeTeamRepository
{
    return new FakeTeamRepository([
        '550e8400-e29b-41d4-a716-446655440000' => new Team(new TeamId('550e8400-e29b-41d4-a716-446655440000'), new TeamName('Engineering'), new TeamSlug('engineering'), 'Tech team', null),
        '660e8400-e29b-41d4-a716-446655440000' => new Team(new TeamId('660e8400-e29b-41d4-a716-446655440000'), new TeamName('Design'), new TeamSlug('design'), 'Design team', null),
        '770e8400-e29b-41d4-a716-446655440000' => new Team(new TeamId('770e8400-e29b-41d4-a716-446655440000'), new TeamName('Engineering Ops'), new TeamSlug('engineering-ops'), 'Ops', null),
    ]);
}

it('searches teams by name', function (): void {
    $handler = new SearchTeamsHandler(searchTeamsRepository());

    $result = $handler->handle(new SearchTeamsQuery('Engineering', [], 50));

    expect($result)->toHaveCount(2)
        ->and($result[0]->name->value)->toBe('Engineering')
        ->and($result[1]->name->value)->toBe('Engineering Ops');
});

it('excludes specified team ids', function (): void {
    $handler = new SearchTeamsHandler(searchTeamsRepository());

    $result = $handler->handle(new SearchTeamsQuery('Engineering', ['550e8400-e29b-41d4-a716-446655440000'], 50));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Engineering Ops');
});

it('respects limit', function (): void {
    $handler = new SearchTeamsHandler(searchTeamsRepository());

    $result = $handler->handle(new SearchTeamsQuery('', [], 2));

    expect($result)->toHaveCount(2);
});

it('returns empty when no matches', function (): void {
    $handler = new SearchTeamsHandler(searchTeamsRepository());

    $result = $handler->handle(new SearchTeamsQuery('nonexistent', [], 50));

    expect($result)->toBeEmpty();
});
