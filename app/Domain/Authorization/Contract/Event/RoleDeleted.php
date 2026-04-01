<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class RoleDeleted implements DomainEvent
{
    public function __construct(
        public string $roleId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
