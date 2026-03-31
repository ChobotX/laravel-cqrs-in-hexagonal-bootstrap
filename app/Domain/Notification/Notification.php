<?php

declare(strict_types=1);

namespace App\Domain\Notification;

use DateTimeImmutable;

final readonly class Notification
{
    public function __construct(
        public NotificationId $id,
        public string $recipientId,
        public NotificationType $type,
        public string $title,
        public string $body,
        public NotificationLevel $level,
        public ?NotificationLink $link,
        public NotificationChannel $channel,
        public bool $isRead,
        public DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $readAt,
    ) {}
}
