<?php

declare(strict_types=1);

namespace App\Domain\Organization\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class OrganizationDeleted implements DomainEvent
{
    public function __construct(
        public string $organizationId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
