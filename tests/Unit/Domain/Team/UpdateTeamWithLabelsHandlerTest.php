<?php

declare(strict_types=1);

use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\Team\Contract\Command\UpdateTeamCommand;
use App\Domain\Team\Contract\Command\UpdateTeamWithLabelsCommand;
use App\Domain\Team\Handler\Command\UpdateTeamWithLabelsHandler;
use Tests\Helper\FakeCommandBus;

it('dispatches UpdateTeamCommand only when labelIds null', function (): void {
    $bus = new FakeCommandBus;
    $handler = new UpdateTeamWithLabelsHandler($bus);

    $handler->handle(new UpdateTeamWithLabelsCommand(
        id: 't-1',
        name: 'Team',
        slug: 'team',
        description: '',
        parentTeamId: null,
        actorId: 'actor',
    ));

    expect($bus->dispatched)->toHaveCount(1)
        ->and($bus->dispatched[0])->toBeInstanceOf(UpdateTeamCommand::class);
});

it('dispatches UpdateTeamCommand then SyncEntityLabelsCommand when labelIds provided', function (): void {
    $bus = new FakeCommandBus;
    $handler = new UpdateTeamWithLabelsHandler($bus);

    $handler->handle(new UpdateTeamWithLabelsCommand(
        id: 't-1',
        name: 'Team',
        slug: 'team',
        description: 'desc',
        parentTeamId: 'parent',
        actorId: 'actor',
        labelIds: ['l-1'],
    ));

    expect($bus->dispatched)->toHaveCount(2)
        ->and($bus->dispatched[0])->toBeInstanceOf(UpdateTeamCommand::class);

    $sync = $bus->dispatched[1];
    assert($sync instanceof SyncEntityLabelsCommand);
    expect($sync->entityType)->toBe('teams')
        ->and($sync->submittedLabelIds)->toBe(['l-1'])
        ->and($sync->actingUserId)->toBe('actor');
});
