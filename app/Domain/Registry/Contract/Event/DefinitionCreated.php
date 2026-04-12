<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when definition created in the Registry context; handled by registered domain event handlers.
 */
final readonly class DefinitionCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $definitionId,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public string $slug,
        /** Human-visible label or title. */
        public string $name,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'definition';
    }

    public function entityId(): string
    {
        return $this->definitionId;
    }
}
