<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/** Emitted when a new SsoConfiguration is created. */
final readonly class SsoConfigurationCreated implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $configurationId,
        public string $providerType,
        public string $slug,
        public string $displayName,
        public bool $enabled,
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
