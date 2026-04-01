<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Label\Contract\Event\LabelCreated;

it('can be constructed with enriched data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');
    $event = new LabelCreated('550e8400-e29b-41d4-a716-446655440000', 'users', 'important', $occurredAt);

    expect($event->labelId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->namespace)->toBe('users')
        ->and($event->name)->toBe('important')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('implements DomainEvent', function (): void {
    $event = new LabelCreated('550e8400-e29b-41d4-a716-446655440000', 'users', 'important', new DateTimeImmutable);

    expect($event)->toBeInstanceOf(DomainEvent::class);
});
