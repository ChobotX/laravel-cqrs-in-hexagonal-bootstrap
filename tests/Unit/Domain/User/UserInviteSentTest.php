<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\User\Contract\Event\UserInviteSent;

it('can be constructed with enriched data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');
    $event = new UserInviteSent(
        '550e8400-e29b-41d4-a716-446655440000',
        'John Doe',
        'https://app.test/invite/abc123',
        'en',
        $occurredAt,
    );

    expect($event->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->userName)->toBe('John Doe')
        ->and($event->inviteLink)->toBe('https://app.test/invite/abc123')
        ->and($event->locale)->toBe('en')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('implements DomainEvent', function (): void {
    $event = new UserInviteSent(
        '550e8400-e29b-41d4-a716-446655440000',
        'John Doe',
        'https://app.test/invite/abc123',
        'en',
        new DateTimeImmutable,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class);
});
