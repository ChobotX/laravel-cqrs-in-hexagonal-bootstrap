<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class NotificationDeleted implements DomainEvent
{
    public function __construct(
        public string $notificationId,
        public string $recipientId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
