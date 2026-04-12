<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when permission override set in the Authorization context; handled by registered domain event handlers.
 */
final readonly class PermissionOverrideSet implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $userId,
        /** Field `permission` for this contract; see module docs for validation rules. */
        public string $permission,
        /** Field `type` for this contract; see module docs for validation rules. */
        public string $type,
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
