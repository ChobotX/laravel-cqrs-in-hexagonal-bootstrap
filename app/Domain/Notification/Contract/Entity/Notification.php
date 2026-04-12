<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Entity;

use App\Domain\Notification\Contract\Enum\NotificationChannel;
use App\Domain\Notification\Contract\ValueObject\NotificationId;
use App\Domain\Notification\Enum\NotificationLevel;
use App\Domain\Notification\ValueObject\NotificationLink;
use App\Domain\Notification\ValueObject\NotificationType;
use DateTimeImmutable;

/**
 * Immutable read-model snapshot of a Notification returned from queries in the Notification context.
 */
final readonly class Notification
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public NotificationId $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $recipientId,
        /** Field `type` for this contract; see module docs for validation rules. */
        public NotificationType $type,
        /** Human-visible label or title. */
        public string $title,
        /** Field `body` for this contract; see module docs for validation rules. */
        public string $body,
        /** Field `level` for this contract; see module docs for validation rules. */
        public NotificationLevel $level,
        /** Optional `link`; null means not provided or not applicable. */
        public ?NotificationLink $link,
        /** Field `channel` for this contract; see module docs for validation rules. */
        public NotificationChannel $channel,
        /** Boolean flag for this state or capability. */
        public bool $isRead,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $createdAt,
        /** Point in time for auditing or ordering. */
        public ?DateTimeImmutable $readAt,
    ) {}
}
