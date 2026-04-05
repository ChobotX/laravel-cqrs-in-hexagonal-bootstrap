<?php

declare(strict_types=1);

use App\Domain\Team\Contract\Command\DeleteTeamCommand;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Event\TeamDeleted;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Handler\Command\DeleteTeamHandler;
use App\Domain\Team\ValueObject\TeamName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTeamRepository;

it('deletes a team and emits event', function (): void {
    $team = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
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

it('deletes a parent team leaving children orphaned', function (): void {
    $parent = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Parent',
        null,
    );
    $child = new Team(
        new TeamId('660e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Backend'),
        new TeamSlug('backend'),
        'Child',
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
    );

    $teamRepo = new FakeTeamRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $parent,
        '660e8400-e29b-41d4-a716-446655440000' => $child,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new DeleteTeamCommand('550e8400-e29b-41d4-a716-446655440000'));

    expect($teamRepo->deleted)->toContain('550e8400-e29b-41d4-a716-446655440000');

    $remainingChild = $teamRepo->findById(new TeamId('660e8400-e29b-41d4-a716-446655440000'));
    assert($remainingChild instanceof Team);
    expect($remainingChild->parentTeamId?->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws when team not found', function (): void {
    $teamRepo = new FakeTeamRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new DeleteTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new DeleteTeamCommand('550e8400-e29b-41d4-a716-446655440000'));
})->throws(TeamNotFoundException::class);
