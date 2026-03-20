<?php

declare(strict_types=1);

use App\Domain\Organization\Exception\TeamNotFoundException;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\Query\GetTeamById\GetTeamByIdHandler;
use App\Domain\Organization\Query\GetTeamById\GetTeamByIdQuery;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamSlug;
use Tests\Helper\FakeTeamRepository;

it('returns team by id', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationId('660e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $handler = new GetTeamByIdHandler($teamRepo);

    $result = $handler->handle(new GetTeamByIdQuery('550e8400-e29b-41d4-a716-446655440000'));

    expect($result->name->value)->toBe('Engineering');
});

it('throws when team not found', function (): void {
    $teamRepo = new FakeTeamRepository;
    $handler = new GetTeamByIdHandler($teamRepo);

    $handler->handle(new GetTeamByIdQuery('550e8400-e29b-41d4-a716-446655440000'));
})->throws(TeamNotFoundException::class);
