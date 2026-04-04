<?php

declare(strict_types=1);

use App\Domain\Label\Command\RemoveLabel\RemoveLabelCommand;
use App\Domain\Label\Command\RemoveLabel\RemoveLabelHandler;
use App\Domain\Label\Contract\Event\LabelDeleted;
use App\Domain\Label\Contract\Event\LabelRemoved;
use App\Domain\Label\Contract\Label;
use App\Domain\Label\Contract\LabelId;
use App\Domain\Label\LabelName;
use App\Domain\Label\LabelNamespace;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeLabelRepository;

it('removes assignment and deletes orphaned label', function (): void {
    $label = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(
        labels: ['550e8400-e29b-41d4-a716-446655440000' => $label],
        assignments: [['labelId' => '550e8400-e29b-41d4-a716-446655440000', 'labelableId' => '770e8400-e29b-41d4-a716-446655440000']],
    );
    $eventCollector = new FakeEventCollector;

    $handler = new RemoveLabelHandler($repository, $eventCollector);

    $handler->handle(new RemoveLabelCommand(
        labelId: '550e8400-e29b-41d4-a716-446655440000',
        labelableId: '770e8400-e29b-41d4-a716-446655440000',
    ));

    expect($eventCollector->collected)->toHaveCount(2);
    expect($eventCollector->collected[0])->toBeInstanceOf(LabelRemoved::class);
    assert($eventCollector->collected[0] instanceof LabelRemoved);
    expect($eventCollector->collected[0]->labelId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->labelableId)->toBe('770e8400-e29b-41d4-a716-446655440000');

    expect($eventCollector->collected[1])->toBeInstanceOf(LabelDeleted::class);
    assert($eventCollector->collected[1] instanceof LabelDeleted);
    expect($eventCollector->collected[1]->labelId)->toBe('550e8400-e29b-41d4-a716-446655440000');

    expect($repository->deleted)->toBe(['550e8400-e29b-41d4-a716-446655440000']);
});

it('removes assignment but keeps label with other assignments', function (): void {
    $label = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(
        labels: ['550e8400-e29b-41d4-a716-446655440000' => $label],
        assignments: [
            ['labelId' => '550e8400-e29b-41d4-a716-446655440000', 'labelableId' => '770e8400-e29b-41d4-a716-446655440000'],
            ['labelId' => '550e8400-e29b-41d4-a716-446655440000', 'labelableId' => '880e8400-e29b-41d4-a716-446655440000'],
        ],
    );
    $eventCollector = new FakeEventCollector;

    $handler = new RemoveLabelHandler($repository, $eventCollector);

    $handler->handle(new RemoveLabelCommand(
        labelId: '550e8400-e29b-41d4-a716-446655440000',
        labelableId: '770e8400-e29b-41d4-a716-446655440000',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(LabelRemoved::class);
    assert($eventCollector->collected[0] instanceof LabelRemoved);
    expect($eventCollector->collected[0]->labelId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->labelableId)->toBe('770e8400-e29b-41d4-a716-446655440000');

    expect($repository->deleted)->toBe([]);
});
