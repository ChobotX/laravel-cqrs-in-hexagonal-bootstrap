<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class DefaultRolesSeeded implements DomainEvent
{
    /**
     * @param  list<string>  $roleIds
     */
    public function __construct(
        public array $roleIds,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
