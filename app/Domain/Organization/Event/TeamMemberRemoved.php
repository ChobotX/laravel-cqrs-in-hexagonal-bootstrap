<?php

declare(strict_types=1);

namespace App\Domain\Organization\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class TeamMemberRemoved implements DomainEvent
{
    public function __construct(
        public string $userId,
        public string $teamId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
