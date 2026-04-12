<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when notification created in the Notification context; handled by registered domain event handlers.
 */
final readonly class NotificationCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $notificationId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $recipientId,
        /** Field `type` for this contract; see module docs for validation rules. */
        public string $type,
        /** Human-visible label or title. */
        public string $title,
        /** Field `body` for this contract; see module docs for validation rules. */
        public string $body,
        /** Field `level` for this contract; see module docs for validation rules. */
        public string $level,
        /** Optional `link`; null means not provided or not applicable. */
        public ?string $link,
        /** Field `channel` for this contract; see module docs for validation rules. */
        public string $channel,
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
