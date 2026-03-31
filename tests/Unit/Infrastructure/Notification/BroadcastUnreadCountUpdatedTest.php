<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Notification\Event\AllNotificationsRead;
use App\Domain\Notification\Event\NotificationDeleted;
use App\Domain\Notification\Event\NotificationRead;
use App\Infrastructure\Notification\Broadcast\UnreadCountUpdatedBroadcast;
use App\Infrastructure\Notification\EventHandler\BroadcastUnreadCountUpdated;
use Tests\Helper\FakeEventsDispatcher;
use Tests\Helper\FakeNotificationRepository;

it('broadcasts unread count on NotificationRead', function (): void {
    $dispatcher = new FakeEventsDispatcher;
    $repo = new FakeNotificationRepository;

    $handler = new BroadcastUnreadCountUpdated($dispatcher, $repo);

    $handler->handle(new NotificationRead(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: new DateTimeImmutable,
    ));

    expect($dispatcher->dispatched)->toHaveCount(1)
        ->and($dispatcher->dispatched[0])->toBeInstanceOf(UnreadCountUpdatedBroadcast::class);
    assert($dispatcher->dispatched[0] instanceof UnreadCountUpdatedBroadcast);
    expect($dispatcher->dispatched[0]->count)->toBe(0);
});

it('broadcasts unread count on AllNotificationsRead', function (): void {
    $dispatcher = new FakeEventsDispatcher;
    $repo = new FakeNotificationRepository;

    $handler = new BroadcastUnreadCountUpdated($dispatcher, $repo);

    $handler->handle(new AllNotificationsRead(
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: new DateTimeImmutable,
    ));

    expect($dispatcher->dispatched)->toHaveCount(1)
        ->and($dispatcher->dispatched[0])->toBeInstanceOf(UnreadCountUpdatedBroadcast::class);
});

it('broadcasts unread count on NotificationDeleted', function (): void {
    $dispatcher = new FakeEventsDispatcher;
    $repo = new FakeNotificationRepository;

    $handler = new BroadcastUnreadCountUpdated($dispatcher, $repo);

    $handler->handle(new NotificationDeleted(
        notificationId: '550e8400-e29b-41d4-a716-446655440000',
        recipientId: '660e8400-e29b-41d4-a716-446655440000',
        occurredAt: new DateTimeImmutable,
    ));

    expect($dispatcher->dispatched)->toHaveCount(1)
        ->and($dispatcher->dispatched[0])->toBeInstanceOf(UnreadCountUpdatedBroadcast::class);
});

it('ignores unrecognized events', function (): void {
    $dispatcher = new FakeEventsDispatcher;
    $repo = new FakeNotificationRepository;

    $handler = new BroadcastUnreadCountUpdated($dispatcher, $repo);

    $unknownEvent = new readonly class(new DateTimeImmutable) implements DomainEvent
    {
        public function __construct(private DateTimeImmutable $occurredAt) {}

        public function occurredAt(): DateTimeImmutable
        {
            return $this->occurredAt;
        }
    };

    $handler->handle($unknownEvent);

    expect($dispatcher->dispatched)->toHaveCount(0);
});
