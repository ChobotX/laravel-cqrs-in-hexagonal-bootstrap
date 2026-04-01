<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\User\Contract\Event\UserCreated;

it('can be constructed with enriched data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');
    $event = new UserCreated('550e8400-e29b-41d4-a716-446655440000', 'John Doe', 'john@example.com', $occurredAt);

    expect($event->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('John Doe')
        ->and($event->email)->toBe('john@example.com')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('implements DomainEvent', function (): void {
    $event = new UserCreated('550e8400-e29b-41d4-a716-446655440000', 'John Doe', 'john@example.com', new DateTimeImmutable);

    expect($event)->toBeInstanceOf(DomainEvent::class);
});
