<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Notification\NotificationBroadcaster;
use DateTimeImmutable;

final class FakeNotificationBroadcaster implements NotificationBroadcaster
{
    /** @var list<array{recipientId: string, notificationId: string, level: string, title: string, body: string, link: ?string, createdAt: DateTimeImmutable}> */
    public array $broadcastedNotifications = [];

    /** @var list<array{recipientId: string, count: int}> */
    public array $broadcastedUnreadCounts = [];

    public function broadcastNewNotification(
        string $recipientId,
        string $notificationId,
        string $level,
        string $title,
        string $body,
        ?string $link,
        DateTimeImmutable $createdAt,
    ): void {
        $this->broadcastedNotifications[] = [
            'recipientId' => $recipientId,
            'notificationId' => $notificationId,
            'level' => $level,
            'title' => $title,
            'body' => $body,
            'link' => $link,
            'createdAt' => $createdAt,
        ];
    }

    public function broadcastUnreadCountUpdated(string $recipientId, int $count): void
    {
        $this->broadcastedUnreadCounts[] = [
            'recipientId' => $recipientId,
            'count' => $count,
        ];
    }
}
