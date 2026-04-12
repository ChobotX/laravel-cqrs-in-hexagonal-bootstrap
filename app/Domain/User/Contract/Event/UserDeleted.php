<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityDeleted;
use DateTimeImmutable;

final readonly class UserDeleted implements DomainEvent, EntityDeleted
{
    use DescribesAction;

    public function __construct(
        public string $userId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function entityId(): string
    {
        return $this->userId;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'user';
    }
}
