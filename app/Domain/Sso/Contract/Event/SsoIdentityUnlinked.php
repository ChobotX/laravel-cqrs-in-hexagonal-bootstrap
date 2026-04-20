<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/** Emitted when a UserSsoIdentity row is removed. */
final readonly class SsoIdentityUnlinked implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $identityId,
        public string $userId,
        public string $configurationId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'sso_identity';
    }

    public function entityId(): string
    {
        return $this->identityId;
    }
}
