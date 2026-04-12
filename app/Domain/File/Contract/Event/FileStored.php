<?php

declare(strict_types=1);

namespace App\Domain\File\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class FileStored implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $fileId,
        public string $namespace,
        public string $originalName,
        public string $storagePath,
        public int $version,
        public string $uploadedBy,
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
