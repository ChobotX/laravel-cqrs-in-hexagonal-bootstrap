<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\User\Contract\Event\PasswordResetRequested;

it('can be constructed with enriched data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');
    $event = new PasswordResetRequested('john@example.com', $occurredAt);

    expect($event->email)->toBe('john@example.com')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('implements DomainEvent', function (): void {
    $event = new PasswordResetRequested('john@example.com', new DateTimeImmutable);

    expect($event)->toBeInstanceOf(DomainEvent::class);
});
