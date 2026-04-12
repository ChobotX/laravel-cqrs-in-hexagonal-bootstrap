<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when notification read in the Notification context; handled by registered domain event handlers.
 */
final readonly class NotificationRead implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $notificationId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $recipientId,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'notification';
    }

    public function entityId(): string
    {
        return $this->notificationId;
    }
}
