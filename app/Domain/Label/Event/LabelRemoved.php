<?php

declare(strict_types=1);

namespace App\Domain\Label\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class LabelRemoved implements DomainEvent
{
    public function __construct(
        public string $labelId,
        public string $labelableId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
