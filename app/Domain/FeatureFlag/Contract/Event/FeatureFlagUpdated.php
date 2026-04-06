<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Event;

use App\Contract\Event\DomainEvent;
use DateTimeImmutable;

final readonly class FeatureFlagUpdated implements DomainEvent
{
    public function __construct(
        public string $key,
        public string $value,
        public bool $enabled,
        public DateTimeImmutable $occurredAt,
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
