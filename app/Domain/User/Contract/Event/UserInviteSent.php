<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when user invite sent in the User context; handled by registered domain event handlers.
 */
final readonly class UserInviteSent implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Human-visible label or title. */
        public string $userName,
        /** Field `inviteLink` for this contract; see module docs for validation rules. */
        public string $inviteLink,
        /** BCP 47 locale code controlling formatting or translations. */
        public string $locale,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
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
