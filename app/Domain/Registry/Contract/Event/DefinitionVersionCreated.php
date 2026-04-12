<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class DefinitionVersionCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $versionId,
        public string $definitionId,
        public int $version,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'definition_version';
    }

    public function entityId(): string
    {
        return $this->versionId;
    }
}
