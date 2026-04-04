<?php

declare(strict_types=1);

use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Query\GetTeamByIdQuery;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Handler\Query\GetTeamByIdHandler;
use App\Domain\Team\ValueObject\TeamName;
use Tests\Helper\FakeTeamRepository;

it('returns team by id', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
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
