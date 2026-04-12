<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when file stored in the File context; handled by registered domain event handlers.
 */
final readonly class FileStored implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $fileId,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Human-visible label or title. */
        public string $originalName,
        /** Filesystem or storage path as understood by infrastructure adapters. */
        public string $storagePath,
        /** Field `version` for this contract; see module docs for validation rules. */
        public int $version,
        /** Field `uploadedBy` for this contract; see module docs for validation rules. */
        public string $uploadedBy,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'file';
    }

    public function entityId(): string
    {
        return $this->fileId;
    }
}
