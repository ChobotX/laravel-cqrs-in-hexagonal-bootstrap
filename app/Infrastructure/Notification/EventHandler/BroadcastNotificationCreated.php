<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\EventHandler;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\DomainEventHandler;
use App\Domain\Notification\Event\NotificationCreated;
use App\Domain\Notification\NotificationChannel;
use App\Domain\Notification\NotificationRepository;
use App\Infrastructure\Notification\Broadcast\NewNotificationBroadcast;
use App\Infrastructure\Notification\Broadcast\UnreadCountUpdatedBroadcast;
use Illuminate\Contracts\Events\Dispatcher;

/** @implements DomainEventHandler<NotificationCreated> */
final readonly class BroadcastNotificationCreated implements DomainEventHandler
{
    public function __construct(
        private Dispatcher $eventDispatcher,
        private NotificationRepository $notificationRepository,
    ) {}

    public function handle(DomainEvent $domainEvent): void
    {
        if ($domainEvent->channel !== NotificationChannel::InApp->value) {
            return;
        }

        $this->eventDispatcher->dispatch(new NewNotificationBroadcast(
            recipientId: $domainEvent->recipientId,
            payload: [
                'id' => $domainEvent->notificationId,
                'level' => $domainEvent->level,
                'title' => $domainEvent->title,
                'body' => $domainEvent->body,
                'link_url' => $domainEvent->link,
                'read_at' => null,
                'created_at' => $domainEvent->occurredAt->format('c'),
            ],
        ));

        $unreadCount = $this->notificationRepository->countUnreadByRecipient($domainEvent->recipientId);

        $this->eventDispatcher->dispatch(new UnreadCountUpdatedBroadcast(
            recipientId: $domainEvent->recipientId,
            count: $unreadCount,
        ));
    }
}
