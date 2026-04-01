<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class RecordShareRevoked implements DomainEvent
{
    public function __construct(
        public string $granteeUserId,
        public string $resourceType,
        public string $resourceId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
