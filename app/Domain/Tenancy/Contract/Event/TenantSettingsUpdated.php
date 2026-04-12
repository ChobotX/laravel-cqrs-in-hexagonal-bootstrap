<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Application\Event\EntityUpdated;
use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when tenant settings updated in the Tenancy context; handled by registered domain event handlers.
 */
final readonly class TenantSettingsUpdated implements DomainEvent, EntityUpdated
{
    use DescribesAction;

    /** @param list<PropertyChange> $changes */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $tenantId,
        /** Array for `changes`; see constructor PHPDoc for structural tags when present. */
        public array $changes,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    /** @return list<PropertyChange> */
    public function changes(): array
    {
        return $this->changes;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'tenant';
    }

    public function entityId(): string
    {
        return $this->tenantId;
    }
}
