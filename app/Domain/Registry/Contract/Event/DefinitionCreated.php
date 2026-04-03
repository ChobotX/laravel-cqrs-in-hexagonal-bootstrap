<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class DefinitionCreated implements DomainEvent
{
    public function __construct(
        public string $definitionId,
        public string $namespace,
        public string $slug,
        public string $name,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
