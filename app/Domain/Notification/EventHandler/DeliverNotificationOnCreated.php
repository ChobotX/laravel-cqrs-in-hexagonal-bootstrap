<?php

declare(strict_types=1);

namespace App\Domain\Notification\EventHandler;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Notification\NotificationBroadcaster;
use App\Domain\Notification\Event\NotificationCreated;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationRepository;

/** @implements DomainEventHandler<NotificationCreated> */
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
