<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityDeleted;
use DateTimeImmutable;

/**
 * Domain event emitted when user deleted in the User context; handled by registered domain event handlers.
 */
final readonly class UserDeleted implements DomainEvent, EntityDeleted
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function entityId(): string
    {
        return $this->userId;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'user';
    }
}
