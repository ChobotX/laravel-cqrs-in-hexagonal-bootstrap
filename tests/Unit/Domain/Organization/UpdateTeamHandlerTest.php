<?php

declare(strict_types=1);

use App\Domain\Organization\Command\UpdateTeam\UpdateTeamCommand;
use App\Domain\Organization\Command\UpdateTeam\UpdateTeamHandler;
use App\Domain\Organization\Event\TeamUpdated;
use App\Domain\Organization\Exception\TeamCycleDetectedException;
use App\Domain\Organization\Exception\TeamNotFoundException;
use App\Domain\Organization\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Organization\OrganizationId;
use App\Domain\Organization\Team;
use App\Domain\Organization\TeamId;
use App\Domain\Organization\TeamName;
use App\Domain\Organization\TeamSlug;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTeamRepository;

function updateTeamExisting(): Team
{
    return new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new OrganizationId('660e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Engineering'),
        new TeamSlug('engineering'),
        'Original',
        null,
    );
}

it('updates a team and emits event', function (): void {
    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => updateTeamExisting()]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        organizationId: '660e8400-e29b-41d4-a716-446655440000',
        name: 'Updated Engineering',
        slug: 'engineering',
        description: 'Updated',
        parentTeamId: null,
    ));

    expect($teamRepo->saved)->toHaveCount(1)
        ->and($teamRepo->saved[0]->name->value)->toBe('Updated Engineering')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TeamUpdated::class);
});

it('throws when team not found', function (): void {
    $teamRepo = new FakeTeamRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        organizationId: '660e8400-e29b-41d4-a716-446655440000',
        name: 'Test',
        slug: 'test',
        description: '',
        parentTeamId: null,
    ));
})->throws(TeamNotFoundException::class);

it('throws when slug already taken by another team', function (): void {
    $otherTeam = new Team(
        new TeamId('770e8400-e29b-41d4-a716-446655440000'),
        new OrganizationId('660e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Other'),
        new TeamSlug('taken'),
        'Other',
        null,
    );

    $teamRepo = new FakeTeamRepository([
        '550e8400-e29b-41d4-a716-446655440000' => updateTeamExisting(),
        '770e8400-e29b-41d4-a716-446655440000' => $otherTeam,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        organizationId: '660e8400-e29b-41d4-a716-446655440000',
        name: 'Engineering',
        slug: 'taken',
        description: '',
        parentTeamId: null,
    ));
})->throws(TeamSlugAlreadyExistsException::class);

it('throws when setting self as parent', function (): void {
    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => updateTeamExisting()]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        organizationId: '660e8400-e29b-41d4-a716-446655440000',
        name: 'Engineering',
        slug: 'engineering',
        description: '',
        parentTeamId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamCycleDetectedException::class);

it('throws when parent team not found', function (): void {
    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => updateTeamExisting()]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        organizationId: '660e8400-e29b-41d4-a716-446655440000',
        name: 'Engineering',
        slug: 'engineering',
        description: '',
        parentTeamId: '880e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamNotFoundException::class);
