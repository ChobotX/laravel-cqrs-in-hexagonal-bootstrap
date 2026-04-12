<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when feature flag reset in the FeatureFlag context; handled by registered domain event handlers.
 */
final readonly class FeatureFlagReset implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        /** Field `key` for this contract; see module docs for validation rules. */
        public string $key,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function entityType(): string
    {
        return 'feature_flag';
    }

    public function entityId(): string
    {
        return $this->key;
    }
}
