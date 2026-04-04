<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Service;

use DateTimeImmutable;

interface NotificationBroadcaster
{
    public function broadcastNewNotification(
        string $recipientId,
        string $notificationId,
        string $level,
        string $title,
        string $body,
        ?string $link,
        DateTimeImmutable $createdAt,
    ): void;

    public function broadcastUnreadCountUpdated(string $recipientId, int $count): void;
}
