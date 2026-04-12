<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Application\Event\EntityUpdated;
use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Domain event emitted when feature flag updated in the FeatureFlag context; handled by registered domain event handlers.
 */
final readonly class FeatureFlagUpdated implements DomainEvent, EntityUpdated
{
    use DescribesAction;

    /** @param list<PropertyChange> $changes */
    public function __construct(
        /** Field `key` for this contract; see module docs for validation rules. */
        public string $key,
        /** Array for `changes`; see constructor PHPDoc for structural tags when present. */
        public array $changes,
        /** Point in time for auditing or ordering. */
        public DateTimeImmutable $occurredAt,
    ) {}

    /** @return list<PropertyChange> */
    public function changes(): array
    {
        return $this->changes;
    }

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
