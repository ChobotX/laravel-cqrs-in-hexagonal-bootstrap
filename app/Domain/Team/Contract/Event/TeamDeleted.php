<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityDeleted;
use DateTimeImmutable;

final readonly class TeamDeleted implements DomainEvent, EntityDeleted
{
    use DescribesAction;

    public function __construct(
        public string $teamId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function entityId(): string
    {
        return $this->teamId;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'team';
    }
}
