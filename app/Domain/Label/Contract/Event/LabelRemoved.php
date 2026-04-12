<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class LabelRemoved implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $labelId,
        public string $labelableId,
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
