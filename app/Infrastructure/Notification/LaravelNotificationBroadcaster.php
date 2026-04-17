<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Notification\Contract\Service\NotificationBroadcaster;
use App\Domain\Tenancy\Contract\Service\TenantContext;
use App\Infrastructure\Notification\Broadcast\NewNotificationBroadcast;
use App\Infrastructure\Notification\Broadcast\UnreadCountUpdatedBroadcast;
use DateTimeImmutable;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class LaravelNotificationBroadcaster implements NotificationBroadcaster
{
    public function __construct(
        private Dispatcher $eventDispatcher,
        private TenantContext $tenantContext,
    ) {}

    public function broadcastNewNotification(
        string $recipientId,
        string $notificationId,
        string $level,
        string $title,
        string $body,
        ?string $link,
        DateTimeImmutable $createdAt,
    ): void {
        $this->eventDispatcher->dispatch(new NewNotificationBroadcast(
            tenantSlug: $this->tenantContext->currentTenantSlug(),
            recipientId: $recipientId,
            payload: [
                'id' => $notificationId,
                'level' => $level,
                'title' => $title,
                'body' => $body,
                'link_url' => $link,
                'read_at' => null,
                'created_at' => $createdAt->format('c'),
            ],
        ));
    }

    public function broadcastUnreadCountUpdated(string $recipientId, int $count): void
    {
        $this->eventDispatcher->dispatch(new UnreadCountUpdatedBroadcast(
            tenantSlug: $this->tenantContext->currentTenantSlug(),
            recipientId: $recipientId,
            count: $count,
        ));
    }
}
