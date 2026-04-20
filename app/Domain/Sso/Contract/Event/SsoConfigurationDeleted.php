<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/** Emitted when an SsoConfiguration is removed. */
final readonly class SsoConfigurationDeleted implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $configurationId,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'sso_configuration';
    }

    public function entityId(): string
    {
        return $this->configurationId;
    }
}
