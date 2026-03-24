<?php

declare(strict_types=1);

use App\Domain\Team\Query\ListTeams\ListTeamsHandler;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use App\Domain\Team\Team;
use App\Domain\Team\TeamId;
use App\Domain\Team\TeamName;
use App\Domain\Team\TeamSlug;
use Tests\Helper\FakeTeamRepository;

it('lists all teams', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $handler = new ListTeamsHandler($teamRepo);

    $result = $handler->handle(new ListTeamsQuery);

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Engineering');
});

it('returns empty when no teams', function (): void {
    $teamRepo = new FakeTeamRepository;
    $handler = new ListTeamsHandler($teamRepo);

    $result = $handler->handle(new ListTeamsQuery);

    expect($result)->toBe([]);
});
