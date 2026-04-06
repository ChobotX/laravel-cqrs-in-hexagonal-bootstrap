<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Service;

interface FeatureFlagCacheInvalidator
{
    public function invalidate(): void;
}
