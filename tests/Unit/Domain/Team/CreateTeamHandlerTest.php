<?php

declare(strict_types=1);

use App\Domain\Team\Command\CreateTeam\CreateTeamCommand;
use App\Domain\Team\Command\CreateTeam\CreateTeamHandler;
use App\Domain\Team\Contract\Event\TeamCreated;
use App\Domain\Team\Contract\TeamId;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Team\Team;
use App\Domain\Team\TeamName;
use App\Domain\Team\TeamSlug;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTeamRepository;

it('creates a team and emits event', function (): void {
    $teamRepo = new FakeTeamRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new CreateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Engineering',
        slug: 'engineering',
        description: 'Engineering team',
        parentTeamId: null,
    ));

    expect($teamRepo->saved)->toHaveCount(1)
        ->and($teamRepo->saved[0]->name->value)->toBe('Engineering')
        ->and($teamRepo->saved[0]->slug->value)->toBe('engineering')
        ->and($teamRepo->saved[0]->parentTeamId)->toBeNull()
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TeamCreated::class);
});

it('creates a team with parent', function (): void {
    $parentTeam = new Team(
        new TeamId('770e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Parent',
        null,
    );

    $teamRepo = new FakeTeamRepository(['770e8400-e29b-41d4-a716-446655440000' => $parentTeam]);
    $eventCollector = new FakeEventCollector;

    $handler = new CreateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new CreateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Backend',
        slug: 'backend',
        description: 'Backend team',
        parentTeamId: '770e8400-e29b-41d4-a716-446655440000',
    ));

    $saved = $teamRepo->saved[0];

    expect($teamRepo->saved)->toHaveCount(1)
        ->and($saved->parentTeamId)->not->toBeNull();

    expect($saved->parentTeamId?->value)->toBe('770e8400-e29b-41d4-a716-446655440000');
});

it('throws when slug already exists', function (): void {
    $existingTeam = new Team(
        new TeamId('770e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Existing'),
        new TeamSlug('engineering'),
        'Existing',
        null,
    );

    $teamRepo = new FakeTeamRepository(['770e8400-e29b-41d4-a716-446655440000' => $existingTeam]);
    $eventCollector = new FakeEventCollector;

    $handler = new CreateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new CreateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'New Engineering',
        slug: 'engineering',
        description: '',
        parentTeamId: null,
    ));
})->throws(TeamSlugAlreadyExistsException::class);

it('throws when parent team not found', function (): void {
    $teamRepo = new FakeTeamRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new CreateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Backend',
        slug: 'backend',
        description: '',
        parentTeamId: '770e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamNotFoundException::class);
