<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\FeatureFlag;

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;

final readonly class FeatureFlagOverrideMapper
{
    public function toDomain(FeatureFlagOverrideModel $featureFlagOverrideModel): FeatureFlagOverride
    {
        return new FeatureFlagOverride(
            key: $featureFlagOverrideModel->key,
            value: $featureFlagOverrideModel->value,
            enabled: $featureFlagOverrideModel->enabled,
        );
    }
}
