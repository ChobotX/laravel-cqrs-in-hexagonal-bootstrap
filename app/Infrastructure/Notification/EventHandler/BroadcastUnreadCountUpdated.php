<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\EventHandler;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Notification\Event\AllNotificationsRead;
use App\Domain\Notification\Event\NotificationDeleted;
use App\Domain\Notification\Event\NotificationRead;
use App\Domain\Notification\NotificationRepository;
use App\Infrastructure\Notification\Broadcast\UnreadCountUpdatedBroadcast;
use Illuminate\Contracts\Events\Dispatcher;

/** @implements DomainEventHandler<DomainEvent> */
final readonly class BroadcastUnreadCountUpdated implements DomainEventHandler
{
    public function __construct(
        private Dispatcher $eventDispatcher,
        private NotificationRepository $notificationRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        $recipientId = $this->resolveRecipientId($domainEvent);

        if ($recipientId === null) {
            return;
        }

        $unreadCount = $this->notificationRepository->countUnreadByRecipient($recipientId);

        $this->eventDispatcher->dispatch(new UnreadCountUpdatedBroadcast(
            recipientId: $recipientId,
            count: $unreadCount,
        ));
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
