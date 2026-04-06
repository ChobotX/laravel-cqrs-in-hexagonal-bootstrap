<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class UserInviteSent implements DomainEvent
{
    public function __construct(
        public string $userId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
