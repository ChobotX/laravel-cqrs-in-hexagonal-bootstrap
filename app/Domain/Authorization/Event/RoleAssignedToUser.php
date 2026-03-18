<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class RoleAssignedToUser implements DomainEvent
{
    public function __construct(
        public string $userId,
        public string $roleId,
        public string $organizationId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
