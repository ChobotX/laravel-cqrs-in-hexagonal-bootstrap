<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\ValueObject\NotificationLink;
use App\Domain\Notification\ValueObject\NotificationType;

it('can be constructed with all properties', function (): void {
    $createdAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');
    $readAt = new DateTimeImmutable('2026-01-15T11:00:00+00:00');

    $notification = new Notification(
        id: new NotificationId('550e8400-e29b-41d4-a716-446655440000'),
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: new NotificationType('user.welcome'),
        title: 'Welcome!',
        body: 'Welcome to the platform.',
        level: NotificationLevel::Info,
        link: new NotificationLink('/profile'),
        channel: NotificationChannel::InApp,
        isRead: true,
        createdAt: $createdAt,
        readAt: $readAt,
    );

    expect($notification->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($notification->recipientId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($notification->type->value)->toBe('user.welcome')
        ->and($notification->title)->toBe('Welcome!')
        ->and($notification->body)->toBe('Welcome to the platform.')
        ->and($notification->level)->toBe(NotificationLevel::Info)
        ->and($notification->link?->value)->toBe('/profile')
        ->and($notification->channel)->toBe(NotificationChannel::InApp)
        ->and($notification->isRead)->toBeTrue()
        ->and($notification->createdAt)->toBe($createdAt)
        ->and($notification->readAt)->toBe($readAt);
});

it('can be constructed with null link and readAt', function (): void {
    $notification = new Notification(
        id: new NotificationId('550e8400-e29b-41d4-a716-446655440000'),
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: new NotificationType('user.welcome'),
        title: 'Welcome!',
        body: 'Welcome to the platform.',
        level: NotificationLevel::Info,
        link: null,
        channel: NotificationChannel::InApp,
        isRead: false,
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
        readAt: null,
    );

    expect($notification->link)->toBeNull()
        ->and($notification->readAt)->toBeNull()
        ->and($notification->isRead)->toBeFalse();
});
