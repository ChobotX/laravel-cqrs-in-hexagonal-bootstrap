<?php

declare(strict_types=1);

namespace App\Domain\Team\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class TeamCreated implements DomainEvent
{
    public function __construct(
        public string $teamId,
        public string $name,
        public string $slug,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
