<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Event;

use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityUpdated;
use DateTimeImmutable;

final readonly class NotificationPreferencesUpdated implements DomainEvent, EntityUpdated
{
    /** @param list<PropertyChange> $changes */
    public function __construct(
        public string $userId,
        public array $changes,
        public DateTimeImmutable $occurredAt,
    ) {}

    /** @return list<PropertyChange> */
    public function changes(): array
    {
        return $this->changes;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
