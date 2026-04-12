<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when label deleted in the Label context; handled by registered domain event handlers.
 */
final readonly class LabelDeleted implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $labelId,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'label';
    }

    public function entityId(): string
    {
        return $this->labelId;
    }
}
