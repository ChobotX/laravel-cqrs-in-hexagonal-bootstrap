<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Notification\Event\AllNotificationsRead;
use App\Domain\Notification\Event\NotificationCreated;
use App\Domain\Notification\Event\NotificationDeleted;
use App\Domain\Notification\Event\NotificationRead;

it('NotificationCreated carries all data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');

    $event = new NotificationCreated(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Welcome to the platform.',
        level: 'info',
        link: '/profile',
        channel: 'in_app',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->notificationId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->recipientId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($event->type)->toBe('user.welcome')
        ->and($event->title)->toBe('Welcome!')
        ->and($event->body)->toBe('Welcome to the platform.')
        ->and($event->level)->toBe('info')
        ->and($event->link)->toBe('/profile')
        ->and($event->channel)->toBe('in_app')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('NotificationCreated supports null link', function (): void {
    $event = new NotificationCreated(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Body',
        level: 'info',
        link: null,
        channel: 'in_app',
        occurredAt: new DateTimeImmutable,
    );

    expect($event->link)->toBeNull();
});

it('NotificationRead carries data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');

    $event = new NotificationRead(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->notificationId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->recipientId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('AllNotificationsRead carries data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');

    $event = new AllNotificationsRead(
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->recipientId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('NotificationDeleted carries data', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');

    $event = new NotificationDeleted(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->notificationId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->recipientId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($event->occurredAt())->toBe($occurredAt);
});
