<?php

declare(strict_types=1);

use App\Domain\Team\Contract\Team;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Contract\TeamSlug;
use App\Domain\Team\Query\CountTeams\CountTeamsHandler;
use App\Domain\Team\Query\CountTeams\CountTeamsQuery;
use App\Domain\Team\TeamName;
use Tests\Helper\FakeTeamRepository;

it('returns the team count from the repository', function (): void {
    $teams = [
        '550e8400-e29b-41d4-a716-446655440000' => new Team(
            new TeamId('550e8400-e29b-41d4-a716-446655440000'),
            new TeamName('Engineering'),
            new TeamSlug('engineering'),
            'Test',
            null,
        ),
    ];

    $handler = new CountTeamsHandler(new FakeTeamRepository($teams));

    expect($handler->handle(new CountTeamsQuery))->toBe(1);
});

it('returns zero when no teams exist', function (): void {
    $handler = new CountTeamsHandler(new FakeTeamRepository);

    expect($handler->handle(new CountTeamsQuery))->toBe(0);
});
