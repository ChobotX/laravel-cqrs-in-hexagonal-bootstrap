<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class AllNotificationsRead implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $recipientId,
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
        return $this->recipientId;
    }
}
