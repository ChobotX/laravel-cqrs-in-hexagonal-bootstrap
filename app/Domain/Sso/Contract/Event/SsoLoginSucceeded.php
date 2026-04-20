<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/** Emitted after a successful SSO login flow. */
final readonly class SsoLoginSucceeded implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $configurationId,
        public string $userId,
        public string $subject,
        public string $email,
        public bool $userProvisioned,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'sso_login';
    }

    public function entityId(): string
    {
        return $this->userId;
    }
}
