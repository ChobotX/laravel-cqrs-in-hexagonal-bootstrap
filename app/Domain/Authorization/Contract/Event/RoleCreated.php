<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when role created in the Authorization context; handled by registered domain event handlers.
 */
final readonly class RoleCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $roleId,
        /** Human-visible label or title. */
        public string $name,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'role';
    }

    public function entityId(): string
    {
        return $this->roleId;
    }
}
