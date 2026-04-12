<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityDeleted;
use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\ValueObject\LabelId;
use App\Domain\Label\EventHandler\CleanupLabelsOnEntityDeleted;
use App\Domain\Label\ValueObject\LabelName;
use App\Domain\Label\ValueObject\LabelNamespace;
use Tests\Helper\FakeLabelRepository;

it('removes assignments and deletes orphaned labels', function (): void {
    $label = new Label(
        new LabelId('550e8400-e29b-41d4-a716-446655440000'),
        new LabelNamespace('users'),
        new LabelName('important'),
    );

    $repository = new FakeLabelRepository(
        labels: ['550e8400-e29b-41d4-a716-446655440000' => $label],
        assignments: [['labelId' => '550e8400-e29b-41d4-a716-446655440000', 'labelableId' => '770e8400-e29b-41d4-a716-446655440000']],
    );

    $handler = new CleanupLabelsOnEntityDeleted($repository);

    $event = new class implements DomainEvent, EntityDeleted
    {
        public function entityId(): string
        {
            return '770e8400-e29b-41d4-a716-446655440000';
        }

        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable('2026-01-15T10:00:00+00:00');
        }

        public function entityType(): string
        {
            return 'test';
        }

        public function actionLabel(): string
        {
            return 'Test Delete';
        }
    };

    $handler->handle($event);

    expect($repository->deleted)->toBe(['550e8400-e29b-41d4-a716-446655440000']);
});

it('leaves labels that are still assigned to other entities', function (): void {
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

    $handler = new CleanupLabelsOnEntityDeleted($repository);

    $event = new class implements DomainEvent, EntityDeleted
    {
        public function entityId(): string
        {
            return '770e8400-e29b-41d4-a716-446655440000';
        }

        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable('2026-01-15T10:00:00+00:00');
        }

        public function entityType(): string
        {
            return 'test';
        }

        public function actionLabel(): string
        {
            return 'Test Delete';
        }
    };

    $handler->handle($event);

    expect($repository->deleted)->toBe([]);
});

it('does nothing when entity has no labels', function (): void {
    $repository = new FakeLabelRepository;

    $handler = new CleanupLabelsOnEntityDeleted($repository);

    $event = new class implements DomainEvent, EntityDeleted
    {
        public function entityId(): string
        {
            return '770e8400-e29b-41d4-a716-446655440000';
        }

        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable('2026-01-15T10:00:00+00:00');
        }

        public function entityType(): string
        {
            return 'test';
        }

        public function actionLabel(): string
        {
            return 'Test Delete';
        }
    };

    $handler->handle($event);

    expect($repository->deleted)->toBe([]);
});

it('ignores events that do not implement EntityDeleted', function (): void {
    $repository = new FakeLabelRepository;

    $handler = new CleanupLabelsOnEntityDeleted($repository);

    $event = new class implements DomainEvent
    {
        public function occurredAt(): DateTimeImmutable
        {
            return new DateTimeImmutable('2026-01-15T10:00:00+00:00');
        }

        public function entityType(): string
        {
            return 'test';
        }

        public function entityId(): string
        {
            return 'test-id';
        }

        public function actionLabel(): string
        {
            return 'Test';
        }
    };

    $handler->handle($event);

    expect($repository->deleted)->toBe([]);
});
