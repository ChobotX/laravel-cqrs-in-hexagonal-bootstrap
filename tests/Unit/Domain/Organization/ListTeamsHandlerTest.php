<?php

declare(strict_types=1);

use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\Query\ListTeams\ListTeamsHandler;
use App\Domain\Organization\Query\ListTeams\ListTeamsQuery;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamSlug;
use Tests\Helper\FakeTeamRepository;

it('lists teams for organization', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationId('660e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $handler = new ListTeamsHandler($teamRepo);

    $result = $handler->handle(new ListTeamsQuery('660e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->name->value)->toBe('Engineering');
});

it('returns empty when no teams', function (): void {
    $teamRepo = new FakeTeamRepository;
    $handler = new ListTeamsHandler($teamRepo);

    $result = $handler->handle(new ListTeamsQuery('660e8400-e29b-41d4-a716-446655440000'));

    expect($result)->toBe([]);
});
