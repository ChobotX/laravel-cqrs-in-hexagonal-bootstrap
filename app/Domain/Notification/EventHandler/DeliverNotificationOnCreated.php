<?php

declare(strict_types=1);

namespace App\Domain\Notification\EventHandler;

use App\Contract\Attribute\RetryPolicy;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\Event\NotificationCreated;
use App\Domain\Notification\Contract\Repository\NotificationRepository;
use App\Domain\Notification\Contract\Service\NotificationBroadcaster;

/** @implements DomainEventHandler<NotificationCreated> */
#[RetryPolicy(tries: 3, backoff: [10, 30, 60], timeout: 30)]
final readonly class DeliverNotificationOnCreated implements DomainEventHandler
{
    public function __construct(
        private NotificationBroadcaster $notificationBroadcaster,
        private NotificationRepository $notificationRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        if ($domainEvent->channel !== NotificationChannel::InApp->value) {
            return;
        }

        $this->notificationBroadcaster->broadcastNewNotification(
            recipientId: $domainEvent->recipientId,
            notificationId: $domainEvent->notificationId,
            level: $domainEvent->level,
            title: $domainEvent->title,
            body: $domainEvent->body,
            link: $domainEvent->link,
            createdAt: $domainEvent->occurredAt,
        );

        $unreadCount = $this->notificationRepository->countUnreadByRecipient($domainEvent->recipientId);

        $this->notificationBroadcaster->broadcastUnreadCountUpdated(
            recipientId: $domainEvent->recipientId,
            count: $unreadCount,
        );
    }
}
