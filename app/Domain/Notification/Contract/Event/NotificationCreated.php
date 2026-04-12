<?php

declare(strict_types=1);

namespace App\Domain\Notification\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class NotificationCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $notificationId,
        public string $recipientId,
        public string $type,
        public string $title,
        public string $body,
        public string $level,
        public ?string $link,
        public string $channel,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'notification';
    }

    public function entityId(): string
    {
        return $this->notificationId;
    }
}
