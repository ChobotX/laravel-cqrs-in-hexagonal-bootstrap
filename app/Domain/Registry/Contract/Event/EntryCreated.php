<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class EntryCreated implements DomainEvent
{
    public function __construct(
        public string $entryId,
        public string $definitionId,
        public int $definitionVersion,
        public string $namespace,
        public string $title,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
