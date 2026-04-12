<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when user created in the User context; handled by registered domain event handlers.
 */
final readonly class UserCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Human-visible label or title. */
        public string $name,
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $email,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
        /** Optional avatarFile identifier when absent. */
        public ?string $avatarFileId = null,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'user';
    }

    public function entityId(): string
    {
        return $this->userId;
    }
}
