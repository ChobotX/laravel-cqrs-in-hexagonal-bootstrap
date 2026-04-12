<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class RoleCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $roleId,
        public string $name,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'role';
    }

    public function entityId(): string
    {
        return $this->roleId;
    }
}
