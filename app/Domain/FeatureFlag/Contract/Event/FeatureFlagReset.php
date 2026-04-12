<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Event;

use App\Application\Event\DescribesAction;
use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class FeatureFlagReset implements DomainEvent
{
    use DescribesAction;

    public function __construct(
        public string $key,
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
