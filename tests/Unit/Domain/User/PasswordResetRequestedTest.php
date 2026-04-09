<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\User\Contract\Event\PasswordResetRequested;

it('can be constructed with enriched data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');
    $event = new PasswordResetRequested(
        '550e8400-e29b-41d4-a716-446655440000',
        'john@example.com',
        'https://app.test/reset/abc123',
        'en',
        $occurredAt,
    );

    expect($event->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->email)->toBe('john@example.com')
        ->and($event->resetLink)->toBe('https://app.test/reset/abc123')
        ->and($event->locale)->toBe('en')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('implements DomainEvent', function (): void {
    $event = new PasswordResetRequested(
        '550e8400-e29b-41d4-a716-446655440000',
        'john@example.com',
        'https://app.test/reset/abc123',
        'en',
        new DateTimeImmutable,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class);
});
