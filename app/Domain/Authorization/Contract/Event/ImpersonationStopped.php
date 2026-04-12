<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when impersonation stopped in the Authorization context; handled by registered domain event handlers.
 */
final readonly class ImpersonationStopped implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $impersonatorId,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $targetUserId,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'user';
    }

    public function entityId(): string
    {
        return $this->targetUserId;
    }
}
