<?php

declare(strict_types=1);

namespace App\Domain\Team\Event;

use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityDeleted;
use DateTimeImmutable;

final readonly class TeamDeleted implements DomainEvent, EntityDeleted
{
    public function __construct(
        public string $teamId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function entityId(): string
    {
        return $this->teamId;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
