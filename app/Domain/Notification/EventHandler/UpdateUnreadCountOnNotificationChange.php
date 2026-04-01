<?php

declare(strict_types=1);

namespace App\Domain\Notification\EventHandler;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Contract\Notification\NotificationBroadcaster;
use App\Domain\Notification\Event\AllNotificationsRead;
use App\Domain\Notification\Event\NotificationDeleted;
use App\Domain\Notification\Event\NotificationRead;
use App\Domain\Notification\NotificationRepository;

/** @implements DomainEventHandler<DomainEvent> */
final readonly class UpdateUnreadCountOnNotificationChange implements DomainEventHandler
{
    public function __construct(
        private NotificationBroadcaster $notificationBroadcaster,
        private NotificationRepository $notificationRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $recipientId = $this->resolveRecipientId($domainEvent);

        if ($recipientId === null) {
            return;
        }

        $unreadCount = $this->notificationRepository->countUnreadByRecipient($recipientId);

        $this->notificationBroadcaster->broadcastUnreadCountUpdated(
            recipientId: $recipientId,
            count: $unreadCount,
        );
    }

    private function resolveRecipientId(DomainEvent $domainEvent): ?string
    {
        return match (true) {
            $domainEvent instanceof NotificationRead => $domainEvent->recipientId,
            $domainEvent instanceof AllNotificationsRead => $domainEvent->recipientId,
            $domainEvent instanceof NotificationDeleted => $domainEvent->recipientId,
            default => null,
        };
    }
}
