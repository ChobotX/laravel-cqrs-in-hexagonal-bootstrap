<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class PermissionOverrideSet implements DomainEvent
{
    public function __construct(
        public string $userId,
        public string $permission,
        public string $type,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
