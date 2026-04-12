<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when team created in the Team context; handled by registered domain event handlers.
 */
final readonly class TeamCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $teamId,
        /** Human-visible label or title. */
        public string $name,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public string $slug,
        /** Point in time for auditing or ordering. */
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
