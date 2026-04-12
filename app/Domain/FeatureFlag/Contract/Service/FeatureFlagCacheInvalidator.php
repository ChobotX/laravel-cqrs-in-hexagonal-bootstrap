<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Service;

/**
 * Domain service contract for feature flag cache invalidator in the FeatureFlag bounded context.
 */
interface FeatureFlagCacheInvalidator
{
    /** Contract operation `invalidate`; see infrastructure for behavior. */
    public function invalidate(): void;
}
