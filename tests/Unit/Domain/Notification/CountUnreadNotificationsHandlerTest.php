<?php

declare(strict_types=1);

use App\Domain\Notification\Notification;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationId;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationType;
use App\Domain\Notification\Query\CountUnreadNotifications\CountUnreadNotificationsHandler;
use App\Domain\Notification\Query\CountUnreadNotifications\CountUnreadNotificationsQuery;
use Tests\Helper\FakeNotificationRepository;

it('returns unread count for recipient', function (): void {
    $unread = new Notification(
        id: new NotificationId('550e8400-e29b-41d4-a716-446655440001'),
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

    $read = new Notification(
        id: new NotificationId('550e8400-e29b-41d4-a716-446655440002'),
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        type: new NotificationType('user.welcome'),
        title: 'Test',
        body: 'Body',
        level: NotificationLevel::Info,
        link: null,
        channel: NotificationChannel::InApp,
        isRead: true,
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
        readAt: new DateTimeImmutable('2026-01-15T11:00:00+00:00'),
    );

    $repo = new FakeNotificationRepository([
        '550e8400-e29b-41d4-a716-446655440001' => $unread,
        '550e8400-e29b-41d4-a716-446655440002' => $read,
    ]);

    $handler = new CountUnreadNotificationsHandler($repo);
    $count = $handler->handle(new CountUnreadNotificationsQuery(userId: '660e8400-e29b-41d4-a716-446655440000'));

    expect($count)->toBe(1);
});

it('returns zero when no unread notifications', function (): void {
    $repo = new FakeNotificationRepository;

    $handler = new CountUnreadNotificationsHandler($repo);
    $count = $handler->handle(new CountUnreadNotificationsQuery(userId: '660e8400-e29b-41d4-a716-446655440000'));

    expect($count)->toBe(0);
});
