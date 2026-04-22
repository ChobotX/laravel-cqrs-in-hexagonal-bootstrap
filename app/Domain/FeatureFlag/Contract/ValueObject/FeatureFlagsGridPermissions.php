<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\ValueObject;

/**
 * Per-actor capability flags shown in the feature-flags grid header.
 */
final readonly class FeatureFlagsGridPermissions
{
    public function __construct(
        public bool $canUpdate,
    ) {}
}
