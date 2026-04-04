<?php

declare(strict_types=1);

use App\Domain\Label\Contract\Command\SyncEntityLabelsCommand;
use App\Domain\Label\Contract\Event\LabelAssigned;
use App\Domain\Label\Contract\Event\LabelDeleted;
use App\Domain\Label\Contract\Event\LabelRemoved;
use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\Handler\Command\SyncEntityLabelsHandler;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use Tests\Helper\FakeAuthorizationChecker;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeLabelRepository;

function createSyncLabel(string $id, string $name): Label
{
    return new Label(new LabelId($id), new LabelNamespace('users'), new LabelName($name));
}

function createSyncLabelsHandler(
    FakeLabelRepository $fakeLabelRepository,
    FakeEventCollector $fakeEventCollector,
    bool $hasPermission = true,
): SyncEntityLabelsHandler {
    $authChecker = new FakeAuthorizationChecker(
        $hasPermission ? ['labels.management.read'] : [],
    );

    return new SyncEntityLabelsHandler($fakeLabelRepository, $authChecker, $fakeEventCollector);
}

it('assigns new labels and removes old labels', function (): void {
    $labelA = createSyncLabel('00000000-0000-0000-0000-00000000000a', 'Label A');
    $labelB = createSyncLabel('00000000-0000-0000-0000-00000000000b', 'Label B');
    $labelC = createSyncLabel('00000000-0000-0000-0000-00000000000c', 'Label C');

    $labelRepo = new FakeLabelRepository(
        labels: ['00000000-0000-0000-0000-00000000000a' => $labelA, '00000000-0000-0000-0000-00000000000b' => $labelB, '00000000-0000-0000-0000-00000000000c' => $labelC],
        assignments: [
            ['labelId' => '00000000-0000-0000-0000-00000000000a', 'labelableId' => 'entity-1'],
            ['labelId' => '00000000-0000-0000-0000-00000000000b', 'labelableId' => 'entity-1'],
            ['labelId' => '00000000-0000-0000-0000-00000000000a', 'labelableId' => 'entity-2'],
        ],
    );
    $eventCollector = new FakeEventCollector;
    $syncEntityLabelsHandler = createSyncLabelsHandler($labelRepo, $eventCollector);

    $syncEntityLabelsHandler->handle(new SyncEntityLabelsCommand(
        entityId: 'entity-1',
        entityType: 'users',
        submittedLabelIds: ['00000000-0000-0000-0000-00000000000b', '00000000-0000-0000-0000-00000000000c'],
        actingUserId: 'user-1',
    ));

    $assignedEvents = array_filter($eventCollector->collected, fn (App\Contract\Event\DomainEvent $domainEvent): bool => $domainEvent instanceof LabelAssigned);
    $removedEvents = array_filter($eventCollector->collected, fn (App\Contract\Event\DomainEvent $domainEvent): bool => $domainEvent instanceof LabelRemoved);

    expect($assignedEvents)->toHaveCount(1)
        ->and($removedEvents)->toHaveCount(1);
});

it('deletes orphaned labels after removal', function (): void {
    $labelA = createSyncLabel('00000000-0000-0000-0000-00000000000a', 'Label A');

    $labelRepo = new FakeLabelRepository(
        labels: ['00000000-0000-0000-0000-00000000000a' => $labelA],
        assignments: [
            ['labelId' => '00000000-0000-0000-0000-00000000000a', 'labelableId' => 'entity-1'],
        ],
    );
    $eventCollector = new FakeEventCollector;
    $syncEntityLabelsHandler = createSyncLabelsHandler($labelRepo, $eventCollector);

    $syncEntityLabelsHandler->handle(new SyncEntityLabelsCommand(
        entityId: 'entity-1',
        entityType: 'users',
        submittedLabelIds: [],
        actingUserId: 'user-1',
    ));

    $deletedEvents = array_filter($eventCollector->collected, fn (App\Contract\Event\DomainEvent $domainEvent): bool => $domainEvent instanceof LabelDeleted);

    expect($labelRepo->deleted)->toContain('00000000-0000-0000-0000-00000000000a')
        ->and($deletedEvents)->toHaveCount(1);
});

it('does nothing when submitted matches current', function (): void {
    $labelA = createSyncLabel('00000000-0000-0000-0000-00000000000a', 'Label A');

    $labelRepo = new FakeLabelRepository(
        labels: ['00000000-0000-0000-0000-00000000000a' => $labelA],
        assignments: [
            ['labelId' => '00000000-0000-0000-0000-00000000000a', 'labelableId' => 'entity-1'],
        ],
    );
    $eventCollector = new FakeEventCollector;
    $syncEntityLabelsHandler = createSyncLabelsHandler($labelRepo, $eventCollector);

    $syncEntityLabelsHandler->handle(new SyncEntityLabelsCommand(
        entityId: 'entity-1',
        entityType: 'users',
        submittedLabelIds: ['00000000-0000-0000-0000-00000000000a'],
        actingUserId: 'user-1',
    ));

    expect($eventCollector->collected)->toBeEmpty();
});

it('skips sync when user lacks permission', function (): void {
    $labelA = createSyncLabel('00000000-0000-0000-0000-00000000000a', 'Label A');

    $labelRepo = new FakeLabelRepository(
        labels: ['00000000-0000-0000-0000-00000000000a' => $labelA],
        assignments: [
            ['labelId' => '00000000-0000-0000-0000-00000000000a', 'labelableId' => 'entity-1'],
        ],
    );
    $eventCollector = new FakeEventCollector;
    $syncEntityLabelsHandler = createSyncLabelsHandler($labelRepo, $eventCollector, hasPermission: false);

    $syncEntityLabelsHandler->handle(new SyncEntityLabelsCommand(
        entityId: 'entity-1',
        entityType: 'users',
        submittedLabelIds: ['00000000-0000-0000-0000-00000000000b'],
        actingUserId: 'user-1',
    ));

    expect($eventCollector->collected)->toBeEmpty();
});
