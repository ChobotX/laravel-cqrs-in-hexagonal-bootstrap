<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class DefinitionDeleted implements DomainEvent
{
    public function __construct(
        public string $definitionId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
