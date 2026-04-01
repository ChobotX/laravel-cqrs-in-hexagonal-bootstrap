<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class UserCreated implements DomainEvent
{
    public function __construct(
        public string $userId,
        public string $name,
        public string $email,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
