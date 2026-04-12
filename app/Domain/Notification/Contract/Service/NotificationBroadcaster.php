<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Service;

use DateTimeImmutable;

/**
 * Domain service contract for notification broadcaster in the Notification bounded context.
 */
interface NotificationBroadcaster
{
    /** Contract operation `broadcastNewNotification`; see infrastructure for behavior. */
    public function broadcastNewNotification(
        string $recipientId,
        string $notificationId,
        string $level,
        string $title,
        string $body,
        ?string $link,
        DateTimeImmutable $createdAt,
    ): void;

    /** Contract operation `broadcastUnreadCountUpdated`; see infrastructure for behavior. */
    public function broadcastUnreadCountUpdated(string $recipientId, int $count): void;
}
