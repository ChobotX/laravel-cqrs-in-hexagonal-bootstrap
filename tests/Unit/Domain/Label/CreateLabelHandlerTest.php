<?php

declare(strict_types=1);

use App\Domain\Label\Command\CreateLabel\CreateLabelCommand;
use App\Domain\Label\Command\CreateLabel\CreateLabelHandler;
use App\Domain\Label\Event\LabelCreated;
use App\Domain\Label\Exception\LabelAlreadyExistsException;
use App\Domain\Label\Label;
use App\Domain\Label\LabelId;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeLabelRepository;

it('saves a label via the repository', function (): void {
    $repository = new FakeLabelRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateLabelHandler($repository, $eventCollector);

    $handler->handle(new CreateLabelCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'users',
        name: 'important',
    ));

    expect($repository->saved)->toHaveCount(1);
    expect($repository->saved[0]->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($repository->saved[0]->namespace->value)->toBe('users')
        ->and($repository->saved[0]->name->value)->toBe('important');
});

it('collects a LabelCreated event', function (): void {
    $repository = new FakeLabelRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new CreateLabelHandler($repository, $eventCollector);

    $handler->handle(new CreateLabelCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'users',
        name: 'important',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(LabelCreated::class);
    assert($eventCollector->collected[0] instanceof LabelCreated);
    expect($eventCollector->collected[0]->labelId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->namespace)->toBe('users')
        ->and($eventCollector->collected[0]->name)->toBe('important')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('throws when label with same namespace and name already exists', function (): void {
    $existing = new Label(
        new LabelId('660e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(['660e8400-e29b-41d4-a716-446655440000' => $existing]);
    $eventCollector = new FakeEventCollector;

    $handler = new CreateLabelHandler($repository, $eventCollector);

    $handler->handle(new CreateLabelCommand(
        id: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'users',
        name: 'important',
    ));
})->throws(LabelAlreadyExistsException::class, 'A label [important] already exists in namespace [users].');
