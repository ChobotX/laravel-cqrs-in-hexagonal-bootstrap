<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\FeatureFlag\Contract\Service\FeatureFlagCacheInvalidator;

final class FakeFeatureFlagCacheInvalidator implements FeatureFlagCacheInvalidator
{
    public int $invalidateCount = 0;

    public function invalidate(): void
    {
        $this->invalidateCount++;
    }
}
