<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Entity;

final readonly class FeatureFlagOverride
{
    public function __construct(
        public string $key,
        public string $value,
        public bool $enabled,
    ) {}
}
