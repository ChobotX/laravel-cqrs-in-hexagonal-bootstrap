<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Entity;

/**
 * Immutable read-model snapshot of a Feature Flag Override returned from queries in the FeatureFlag context.
 */
final readonly class FeatureFlagOverride
{
    public function __construct(
        /** Field `key` for this contract; see module docs for validation rules. */
        public string $key,
        /** Field `value` for this contract; see module docs for validation rules. */
        public string $value,
        /** Field `enabled` for this contract; see module docs for validation rules. */
        public bool $enabled,
    ) {}
}
