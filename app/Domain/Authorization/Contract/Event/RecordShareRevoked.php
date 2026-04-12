<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when record share revoked in the Authorization context; handled by registered domain event handlers.
 */
final readonly class RecordShareRevoked implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $granteeUserId,
        /** Classifier string or type discriminator. */
        public string $resourceType,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $resourceId,
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
