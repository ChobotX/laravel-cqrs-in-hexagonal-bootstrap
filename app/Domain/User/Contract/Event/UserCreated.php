<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class UserCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $userId,
        public string $name,
        public string $email,
        public DateTimeImmutable $occurredAt,
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
