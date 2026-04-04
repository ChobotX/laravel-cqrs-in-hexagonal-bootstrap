<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\Command\MarkAllNotificationsAsReadCommand;
use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Event\AllNotificationsRead;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\Handler\Command\MarkAllNotificationsAsReadHandler;
use App\Domain\Notification\ValueObject\NotificationType;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeNotificationRepository;

it('marks all notifications as read for the user', function (): void {
    $notification = new Notification(
        id: new NotificationId('00000000-0000-0000-0000-000000000001'),
        recipientId: '550e8400-e29b-41d4-a716-446655440000',
        type: new NotificationType('test.notification'),
        title: 'Test',
        body: 'Test body',
        level: NotificationLevel::Info,
        link: null,
        channel: NotificationChannel::InApp,
        isRead: false,
        createdAt: new DateTimeImmutable('2026-01-01 00:00:00'),
        readAt: null,
    );
    $repo = new FakeNotificationRepository([
        '00000000-0000-0000-0000-000000000001' => $notification,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new MarkAllNotificationsAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkAllNotificationsAsReadCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($repo->markedAllAsReadForRecipients)->toHaveCount(1)
        ->and($repo->markedAllAsReadForRecipients[0])->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('collects AllNotificationsRead event', function (): void {
    $notification = new Notification(
        id: new NotificationId('00000000-0000-0000-0000-000000000001'),
        recipientId: '550e8400-e29b-41d4-a716-446655440000',
        type: new NotificationType('test.notification'),
        title: 'Test',
        body: 'Test body',
        level: NotificationLevel::Info,
        link: null,
        channel: NotificationChannel::InApp,
        isRead: false,
        createdAt: new DateTimeImmutable('2026-01-01 00:00:00'),
        readAt: null,
    );
    $repo = new FakeNotificationRepository([
        '00000000-0000-0000-0000-000000000001' => $notification,
    ]);
    $eventCollector = new FakeEventCollector;

    $handler = new MarkAllNotificationsAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkAllNotificationsAsReadCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(AllNotificationsRead::class);
    assert($eventCollector->collected[0] instanceof AllNotificationsRead);
    expect($eventCollector->collected[0]->recipientId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});

it('skips event when no unread notifications exist', function (): void {
    $repo = new FakeNotificationRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new MarkAllNotificationsAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkAllNotificationsAsReadCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($repo->markedAllAsReadForRecipients)->toBeEmpty()
        ->and($eventCollector->collected)->toBeEmpty();
});
