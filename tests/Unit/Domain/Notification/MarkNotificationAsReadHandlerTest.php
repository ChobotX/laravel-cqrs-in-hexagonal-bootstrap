<?php

declare(strict_types=1);

use App\Domain\Notification\Command\MarkNotificationAsRead\MarkNotificationAsReadHandler;
use App\Domain\Notification\Contract\Command\MarkNotificationAsRead\MarkNotificationAsReadCommand;
use App\Domain\Notification\Contract\Event\NotificationRead;
use App\Domain\Notification\Contract\Notification;
use App\Domain\Notification\Contract\NotificationChannel;
use App\Domain\Notification\Contract\NotificationId;
use App\Domain\Notification\Exception\NotificationNotFoundException;
use App\Domain\Notification\Exception\NotificationOwnershipException;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\NotificationType;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeNotificationRepository;

function createUnreadNotification(string $id, string $recipientId): Notification
{
    return new Notification(
        id: new NotificationId($id),
        recipientId: $recipientId,
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
}

function createReadNotification(string $id, string $recipientId): Notification
{
    return new Notification(
        id: new NotificationId($id),
        recipientId: $recipientId,
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
}

it('marks notification as read', function (): void {
    $notification = createUnreadNotification('550e8400-e29b-41d4-a716-446655440000', '660e8400-e29b-41d4-a716-446655440000');
    $repo = new FakeNotificationRepository(['550e8400-e29b-41d4-a716-446655440000' => $notification]);
    $eventCollector = new FakeEventCollector;

    $handler = new MarkNotificationAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkNotificationAsReadCommand(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
    ));

    expect($repo->markedAsRead)->toHaveCount(1)
        ->and($repo->markedAsRead[0]['id'])->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('collects NotificationRead event', function (): void {
    $notification = createUnreadNotification('550e8400-e29b-41d4-a716-446655440000', '660e8400-e29b-41d4-a716-446655440000');
    $repo = new FakeNotificationRepository(['550e8400-e29b-41d4-a716-446655440000' => $notification]);
    $eventCollector = new FakeEventCollector;

    $handler = new MarkNotificationAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkNotificationAsReadCommand(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(NotificationRead::class);
    assert($eventCollector->collected[0] instanceof NotificationRead);
    expect($eventCollector->collected[0]->notificationId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->recipientId)->toBe('660e8400-e29b-41d4-a716-446655440000');
});

it('skips if already read', function (): void {
    $notification = createReadNotification('550e8400-e29b-41d4-a716-446655440000', '660e8400-e29b-41d4-a716-446655440000');
    $repo = new FakeNotificationRepository(['550e8400-e29b-41d4-a716-446655440000' => $notification]);
    $eventCollector = new FakeEventCollector;

    $handler = new MarkNotificationAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkNotificationAsReadCommand(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
    ));

    expect($repo->markedAsRead)->toHaveCount(0)
        ->and($eventCollector->collected)->toHaveCount(0);
});

it('throws when notification not found', function (): void {
    $repo = new FakeNotificationRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new MarkNotificationAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkNotificationAsReadCommand(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        userId: '660e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(NotificationNotFoundException::class, 'Notification with id [550e8400-e29b-41d4-a716-446655440000] not found.');

it('throws when user does not own notification', function (): void {
    $notification = createUnreadNotification('550e8400-e29b-41d4-a716-446655440000', '660e8400-e29b-41d4-a716-446655440000');
    $repo = new FakeNotificationRepository(['550e8400-e29b-41d4-a716-446655440000' => $notification]);
    $eventCollector = new FakeEventCollector;

    $handler = new MarkNotificationAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkNotificationAsReadCommand(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        userId: '770e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(NotificationOwnershipException::class, 'User does not own notification [550e8400-e29b-41d4-a716-446655440000].');
