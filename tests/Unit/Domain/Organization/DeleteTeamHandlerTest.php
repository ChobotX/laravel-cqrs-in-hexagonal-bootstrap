<?php

declare(strict_types=1);

use App\Domain\Organization\Command\DeleteTeam\DeleteTeamCommand;
use App\Domain\Organization\Command\DeleteTeam\DeleteTeamHandler;
use App\Domain\Organization\Event\TeamDeleted;
use App\Domain\Organization\Exception\TeamNotFoundException;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamSlug;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTeamRepository;

it('deletes a team and emits event', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationId('660e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Test',
        null,
    );

    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => $team]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new DeleteTeamCommand('550e8400-e29b-41d4-a716-446655440000'));

    expect($teamRepo->deleted)->toHaveCount(1)
        ->and($teamRepo->deleted[0])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TeamDeleted::class);
});

it('throws when team not found', function (): void {
    $teamRepo = new FakeTeamRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new DeleteTeamCommand('550e8400-e29b-41d4-a716-446655440000'));
})->throws(TeamNotFoundException::class);
