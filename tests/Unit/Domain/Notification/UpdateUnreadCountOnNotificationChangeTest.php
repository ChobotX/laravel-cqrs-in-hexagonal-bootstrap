<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Notification\Event\AllNotificationsRead;
use App\Domain\Notification\Event\NotificationDeleted;
use App\Domain\Notification\Event\NotificationRead;
use App\Domain\Notification\EventHandler\UpdateUnreadCountOnNotificationChange;
use Tests\Helper\FakeNotificationBroadcaster;
use Tests\Helper\FakeNotificationRepository;

it('broadcasts unread count on NotificationRead', function (): void {
    $broadcaster = new FakeNotificationBroadcaster;
    $repo = new FakeNotificationRepository;

    $handler = new UpdateUnreadCountOnNotificationChange($broadcaster, $repo);

    $handler->handle(new NotificationRead(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($broadcaster->broadcastedUnreadCounts)->toHaveCount(1)
        ->and($broadcaster->broadcastedUnreadCounts[0]['recipientId'])->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($broadcaster->broadcastedUnreadCounts[0]['count'])->toBe(0);
});

it('broadcasts unread count on AllNotificationsRead', function (): void {
    $broadcaster = new FakeNotificationBroadcaster;
    $repo = new FakeNotificationRepository;

    $handler = new UpdateUnreadCountOnNotificationChange($broadcaster, $repo);

    $handler->handle(new AllNotificationsRead(
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($broadcaster->broadcastedUnreadCounts)->toHaveCount(1);
});

it('broadcasts unread count on NotificationDeleted', function (): void {
    $broadcaster = new FakeNotificationBroadcaster;
    $repo = new FakeNotificationRepository;

    $handler = new UpdateUnreadCountOnNotificationChange($broadcaster, $repo);

    $handler->handle(new NotificationDeleted(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($broadcaster->broadcastedUnreadCounts)->toHaveCount(1);
});

it('ignores unrecognized events', function (): void {
    $broadcaster = new FakeNotificationBroadcaster;
    $repo = new FakeNotificationRepository;

    $handler = new UpdateUnreadCountOnNotificationChange($broadcaster, $repo);

    $unknownEvent = new readonly class(new DateTimeImmutable('2026-01-15T10:00:00+00:00')) implements DomainEvent
    {
        public function __construct(private DateTimeImmutable $occurredAt) {}

        public function occurredAt(): DateTimeImmutable
        {
            return $this->occurredAt;
        }
    };

    $handler->handle($unknownEvent);

    expect($broadcaster->broadcastedUnreadCounts)->toHaveCount(0);
});
