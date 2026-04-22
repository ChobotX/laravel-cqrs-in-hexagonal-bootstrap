<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\ValueObject;

/**
 * Pre-projected row for the feature-flags grid. String scalars only so the
 * presentation layer can wrap routes without touching domain entities.
 */
final readonly class FeatureFlagGridRow
{
    public function __construct(
        public string $key,
        public string $label,
        public string $description,
        public string $type,
        public string $group,
        public string $groupLabel,
        public bool $enabled,
        public string $value,
        public bool $isOverridden,
        public bool $hasOptions,
    ) {}
}
