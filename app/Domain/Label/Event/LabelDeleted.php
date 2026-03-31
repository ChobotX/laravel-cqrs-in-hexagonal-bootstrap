<?php

declare(strict_types=1);

namespace App\Domain\Label\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class LabelDeleted implements DomainEvent
{
    public function __construct(
        public string $labelId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
