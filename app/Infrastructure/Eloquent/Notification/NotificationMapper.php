<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Notification;

use App\Domain\Notification\Contract\Entity\Notification;
use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\ValueObject\NotificationLink;
use App\Domain\Notification\ValueObject\NotificationType;
use DateTimeImmutable;

final readonly class NotificationMapper
{
    public function toDomain(NotificationModel $notificationModel): Notification
    {
        return new Notification(
            id: new NotificationId($notificationModel->id),
            recipientId: $notificationModel->recipient_id,
            type: new NotificationType($notificationModel->type),
            title: $notificationModel->title,
            body: $notificationModel->body,
            level: NotificationLevel::from($notificationModel->level),
            link: $notificationModel->link !== null ? new NotificationLink($notificationModel->link) : null,
            channel: NotificationChannel::from($notificationModel->channel),
            isRead: $notificationModel->is_read,
            createdAt: new DateTimeImmutable($notificationModel->created_at->toIso8601String()),
            readAt: $notificationModel->read_at?->toDateTimeImmutable(),
        );
    }
}
