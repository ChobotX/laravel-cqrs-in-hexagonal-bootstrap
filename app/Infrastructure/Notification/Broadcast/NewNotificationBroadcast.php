<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\Broadcast;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

final readonly class NewNotificationBroadcast implements ShouldBroadcast
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        private string $tenantSlug,
        private string $recipientId,
        public array $payload,
    ) {}

    /** @return list<Channel> */
    public function broadcastOn(): array
    {
        return [new PrivateChannel(sprintf('%s.notifications.%s', $this->tenantSlug, $this->recipientId))];
    }

    public function broadcastAs(): string
    {
        return 'NotificationReceived';
    }
}
