<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class LabelCreated implements DomainEvent
{
    public function __construct(
        public string $labelId,
        public string $namespace,
        public string $name,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
