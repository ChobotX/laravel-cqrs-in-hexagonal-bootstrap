<?php

declare(strict_types=1);

use App\Domain\Label\Command\AssignLabel\AssignLabelCommand;
use App\Domain\Label\Command\AssignLabel\AssignLabelHandler;
use App\Domain\Label\Contract\Event\LabelAssigned;
use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\Exception\LabelNamespaceMismatchException;
use App\Domain\Label\Exception\LabelNotFoundException;
use App\Domain\Label\Label;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeLabelRepository;

it('assigns a label and collects an event', function (): void {
    $label = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(['550e8400-e29b-41d4-a716-446655440000' => $label]);
    $eventCollector = new FakeEventCollector;

    $handler = new AssignLabelHandler($repository, $eventCollector);

    $handler->handle(new AssignLabelCommand(
        labelId: '550e8400-e29b-41d4-a716-446655440000',
        labelableId: '770e8400-e29b-41d4-a716-446655440000',
        expectedNamespace: 'users',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(LabelAssigned::class);
    assert($eventCollector->collected[0] instanceof LabelAssigned);
    expect($eventCollector->collected[0]->labelId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->labelableId)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws when label is not found', function (): void {
    $repository = new FakeLabelRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new AssignLabelHandler($repository, $eventCollector);

    $handler->handle(new AssignLabelCommand(
        labelId: '550e8400-e29b-41d4-a716-446655440000',
        labelableId: '770e8400-e29b-41d4-a716-446655440000',
        expectedNamespace: 'users',
    ));
})->throws(LabelNotFoundException::class, 'Label [550e8400-e29b-41d4-a716-446655440000] not found.');

it('throws when namespace does not match', function (): void {
    $label = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('teams'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(['550e8400-e29b-41d4-a716-446655440000' => $label]);
    $eventCollector = new FakeEventCollector;

    $handler = new AssignLabelHandler($repository, $eventCollector);

    $handler->handle(new AssignLabelCommand(
        labelId: '550e8400-e29b-41d4-a716-446655440000',
        labelableId: '770e8400-e29b-41d4-a716-446655440000',
        expectedNamespace: 'users',
    ));
})->throws(LabelNamespaceMismatchException::class, 'Label [550e8400-e29b-41d4-a716-446655440000] belongs to namespace [teams], expected [users].');
