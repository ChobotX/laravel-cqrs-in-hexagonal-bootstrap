<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\Event\NotificationCreated;
use App\Domain\Notification\Contract\Notification;
use App\Domain\Notification\Contract\NotificationChannel;
use App\Domain\Notification\Contract\NotificationId;
use App\Domain\Notification\EventHandler\DeliverNotificationOnCreated;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationType;
use Tests\Helper\FakeNotificationBroadcaster;
use Tests\Helper\FakeNotificationRepository;

it('broadcasts notification and unread count for in_app channel', function (): void {
    $broadcaster = new FakeNotificationBroadcaster;

    $notification = new Notification(
        id: new NotificationId('550e8400-e29b-41d4-a716-446655440000'),
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: new NotificationType('user.welcome'),
        title: 'Test',
        body: 'Body',
        level: NotificationLevel::Info,
        link: null,
        channel: NotificationChannel::InApp,
        isRead: false,
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
        readAt: null,
    );

    $repo = new FakeNotificationRepository([
        '550e8400-e29b-41d4-a716-446655440000' => $notification,
    ]);

    $handler = new DeliverNotificationOnCreated($broadcaster, $repo);

    $event = new NotificationCreated(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Body',
        level: 'info',
        link: null,
        channel: 'in_app',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    );

    $handler->handle($event);

    expect($broadcaster->broadcastedNotifications)->toHaveCount(1)
        ->and($broadcaster->broadcastedNotifications[0]['recipientId'])->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($broadcaster->broadcastedNotifications[0]['notificationId'])->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($broadcaster->broadcastedUnreadCounts)->toHaveCount(1)
        ->and($broadcaster->broadcastedUnreadCounts[0]['recipientId'])->toBe('660e8400-e29b-41d4-a716-446655440000');
});

it('skips broadcast for non in_app channel', function (): void {
    $broadcaster = new FakeNotificationBroadcaster;
    $repo = new FakeNotificationRepository;

    $handler = new DeliverNotificationOnCreated($broadcaster, $repo);

    $event = new NotificationCreated(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: 'user.welcome',
        title: 'Welcome!',
        body: 'Body',
        level: 'info',
        link: null,
        channel: 'email',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    );

    $handler->handle($event);

    expect($broadcaster->broadcastedNotifications)->toHaveCount(0)
        ->and($broadcaster->broadcastedUnreadCounts)->toHaveCount(0);
});
