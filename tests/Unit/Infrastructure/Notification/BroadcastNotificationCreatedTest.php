<?php

declare(strict_types=1);

use App\Domain\Notification\Event\NotificationCreated;
use App\Domain\Notification\Notification;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationId;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationType;
use App\Infrastructure\Notification\Broadcast\NewNotificationBroadcast;
use App\Infrastructure\Notification\Broadcast\UnreadCountUpdatedBroadcast;
use App\Infrastructure\Notification\EventHandler\BroadcastNotificationCreated;
use Tests\Helper\FakeEventsDispatcher;
use Tests\Helper\FakeNotificationRepository;

it('broadcasts notification and unread count for in_app channel', function (): void {
    $dispatcher = new FakeEventsDispatcher;

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

    $handler = new BroadcastNotificationCreated($dispatcher, $repo);

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

    expect($dispatcher->dispatched)->toHaveCount(2)
        ->and($dispatcher->dispatched[0])->toBeInstanceOf(NewNotificationBroadcast::class)
        ->and($dispatcher->dispatched[1])->toBeInstanceOf(UnreadCountUpdatedBroadcast::class);
});

it('skips broadcast for non in_app channel', function (): void {
    $dispatcher = new FakeEventsDispatcher;
    $repo = new FakeNotificationRepository;

    $handler = new BroadcastNotificationCreated($dispatcher, $repo);

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

    expect($dispatcher->dispatched)->toHaveCount(0);
});
