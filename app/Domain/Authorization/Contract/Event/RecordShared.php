<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when record shared in the Authorization context; handled by registered domain event handlers.
 */
final readonly class RecordShared implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $granteeUserId,
        /** Classifier string or type discriminator. */
        public string $resourceType,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $resourceId,
        /** Field `action` for this contract; see module docs for validation rules. */
        public string $action,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $grantorUserId,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return $this->resourceType;
    }

    public function entityId(): string
    {
        return $this->resourceId;
    }
}
