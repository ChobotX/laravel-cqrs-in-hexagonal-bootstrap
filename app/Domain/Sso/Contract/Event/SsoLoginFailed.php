<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/** Emitted when an SSO login flow is rejected. */
final readonly class SsoLoginFailed implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $configurationId,
        public string $reason,
        public ?string $email,
        public ?string $subject,
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
        return $this->configurationId;
    }
}
