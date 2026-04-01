<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\User\Contract\Event\UserUpdated;

it('can be constructed with enriched data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');
    $event = new UserUpdated('550e8400-e29b-41d4-a716-446655440000', 'Jane Doe', 'jane@example.com', $occurredAt);

    expect($event->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('Jane Doe')
        ->and($event->email)->toBe('jane@example.com')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('implements DomainEvent', function (): void {
    $event = new UserUpdated('550e8400-e29b-41d4-a716-446655440000', 'Jane Doe', 'jane@example.com', new DateTimeImmutable);

    expect($event)->toBeInstanceOf(DomainEvent::class);
});
