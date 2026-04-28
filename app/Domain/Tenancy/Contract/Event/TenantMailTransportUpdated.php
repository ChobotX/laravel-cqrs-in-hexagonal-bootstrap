<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Application\Event\EntityUpdated;
use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted whenever the per-tenant mail transport configuration changes (including reverts to default).
 */
final readonly class TenantMailTransportUpdated implements DomainEvent, EntityUpdated
{
    use DescribesAction;

    /** @param list<PropertyChange> $changes */
    public function __construct(
        /** Stable identifier of the tenant whose transport changed. */
        public string $tenantId,
        /** Field-level diff of the transport (password is redacted). */
        public array $changes,
        /** Point in time the change occurred. */
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
        return 'tenant_mail_transport';
    }

    public function entityId(): string
    {
        return $this->tenantId;
    }
}
