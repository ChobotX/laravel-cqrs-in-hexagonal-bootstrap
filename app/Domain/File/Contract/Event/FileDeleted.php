<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityDeleted;
use DateTimeImmutable;

/**
 * Domain event emitted when file deleted in the File context; handled by registered domain event handlers.
 */
final readonly class FileDeleted implements DomainEvent, EntityDeleted
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $fileId,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function entityId(): string
    {
        return $this->fileId;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'file';
    }
}
