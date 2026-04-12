<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class TeamCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $teamId,
        public string $name,
        public string $slug,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'team';
    }

    public function entityId(): string
    {
        return $this->teamId;
    }
}
