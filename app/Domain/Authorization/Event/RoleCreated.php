<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class RoleCreated implements DomainEvent
{
    public function __construct(
        public string $roleId,
        public string $name,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
