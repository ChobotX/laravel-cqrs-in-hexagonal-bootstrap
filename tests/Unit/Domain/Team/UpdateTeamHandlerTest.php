<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Domain\Team\Constant\TeamFields;
use App\Domain\Team\Contract\Command\UpdateTeamCommand;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Event\TeamUpdated;
use App\Domain\Team\Contract\ValueObject\TeamId;
use App\Domain\Team\Contract\ValueObject\TeamSlug;
use App\Domain\Team\Exception\TeamCycleDetectedException;
use App\Domain\Team\Exception\TeamNotFoundException;
use App\Domain\Team\Exception\TeamSlugAlreadyExistsException;
use App\Domain\Team\Handler\Command\UpdateTeamHandler;
use App\Domain\Team\ValueObject\TeamName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTeamRepository;

function updateTeamExisting(): Team
{
    return new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
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
        name: 'Updated Engineering',
        slug: 'engineering',
        description: 'Updated',
        parentTeamId: null,
    ));

    expect($teamRepo->saved)->toHaveCount(1)
        ->and($teamRepo->saved[0]->name->value)->toBe('Updated Engineering')
        ->and($eventCollector->collected)->toHaveCount(1)
        ->and($eventCollector->collected[0])->toBeInstanceOf(TeamUpdated::class);
    assert($eventCollector->collected[0] instanceof TeamUpdated);
    expect($eventCollector->collected[0]->teamId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->changes())->toEqual([
            new PropertyChange(TeamFields::NAME, 'Engineering', 'Updated Engineering'),
            new PropertyChange(TeamFields::DESCRIPTION, 'Original', 'Updated'),
        ]);
});

it('does not save or collect event when data is unchanged', function (): void {
    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => updateTeamExisting()]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Engineering',
        slug: 'engineering',
        description: 'Original',
        parentTeamId: null,
    ));

    expect($teamRepo->saved)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('tracks parent team change', function (): void {
    $parent = new Team(
        new TeamId('770e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Parent'),
        new TeamSlug('parent'),
        'Parent team',
        null,
    );

    $teamRepo = new FakeTeamRepository([
        '550e8400-e29b-41d4-a716-446655440000' => updateTeamExisting(),
        '770e8400-e29b-41d4-a716-446655440000' => $parent,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Engineering',
        slug: 'engineering',
        description: 'Original',
        parentTeamId: '770e8400-e29b-41d4-a716-446655440000',
    ));

    expect($teamRepo->saved)->toHaveCount(1)
        ->and($teamRepo->saved[0]->parentTeamId?->value)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected)->toHaveCount(1);
    assert($eventCollector->collected[0] instanceof TeamUpdated);
    expect($eventCollector->collected[0]->changes())->toEqual([
        new PropertyChange(TeamFields::PARENT_TEAM_ID, null, '770e8400-e29b-41d4-a716-446655440000'),
    ]);
});

it('allows keeping the same slug for the same team', function (): void {
    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => updateTeamExisting()]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'New Name',
        slug: 'engineering',
        description: 'Changed description only',
        parentTeamId: null,
    ));

    expect($teamRepo->saved)->toHaveCount(1)
        ->and($teamRepo->saved[0]->name->value)->toBe('New Name')
        ->and($teamRepo->saved[0]->slug->value)->toBe('engineering');
});

it('throws when team not found', function (): void {
    $teamRepo = new FakeTeamRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Test',
        slug: 'test',
        description: '',
        parentTeamId: null,
    ));
})->throws(TeamNotFoundException::class);

it('throws when slug already taken by another team', function (): void {
    $otherTeam = new Team(
        new TeamId('770e8400-e29b-41d4-a716-446655440000'),
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
        name: 'Engineering',
        slug: 'engineering',
        description: '',
        parentTeamId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamCycleDetectedException::class);

it('throws when setting descendant as parent creates cycle', function (): void {
    $grandparent = new Team(
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Grandparent'),
        new TeamSlug('grandparent'),
        '',
        null,
    );
    $parent = new Team(
        new TeamId('660e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Parent'),
        new TeamSlug('parent'),
        '',
        new TeamId('550e8400-e29b-41d4-a716-446655440000'),
    );
    $child = new Team(
        new TeamId('770e8400-e29b-41d4-a716-446655440000'),
        new TeamName('Child'),
        new TeamSlug('child'),
        '',
        new TeamId('660e8400-e29b-41d4-a716-446655440000'),
    );

    $teamRepo = new FakeTeamRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $grandparent,
        '660e8400-e29b-41d4-a716-446655440000' => $parent,
        '770e8400-e29b-41d4-a716-446655440000' => $child,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Grandparent',
        slug: 'grandparent',
        description: '',
        parentTeamId: '770e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamCycleDetectedException::class);

it('throws when parent team not found', function (): void {
    $teamRepo = new FakeTeamRepository(['550e8400-e29b-41d4-a716-446655440000' => updateTeamExisting()]);
    $eventCollector = new FakeEventCollector;

    $handler = new UpdateTeamHandler($teamRepo, $eventCollector);

    $handler->handle(new UpdateTeamCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Engineering',
        slug: 'engineering',
        description: '',
        parentTeamId: '880e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(TeamNotFoundException::class);
