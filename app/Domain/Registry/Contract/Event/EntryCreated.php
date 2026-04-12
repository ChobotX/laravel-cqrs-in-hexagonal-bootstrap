<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when entry created in the Registry context; handled by registered domain event handlers.
 */
final readonly class EntryCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Registry entry row id (UUID) for the new entry. */
        public string $entryId,
        /** Parent definition id (UUID) the entry belongs to. */
        public string $definitionId,
        /** Monotonic or semantic version number for the resource. */
        public int $definitionVersion,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Human-visible label or title. */
        public string $title,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'entry';
    }

    public function entityId(): string
    {
        return $this->entryId;
    }
}
