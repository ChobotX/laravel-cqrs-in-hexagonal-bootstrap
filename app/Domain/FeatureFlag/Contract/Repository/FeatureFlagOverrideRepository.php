<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Repository;

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;

interface FeatureFlagOverrideRepository
{
    /**
     * @return list<FeatureFlagOverride>
     */
    public function findAll(): array;

    public function findByKey(FlagKey $flagKey): ?FeatureFlagOverride;

    public function save(FeatureFlagOverride $featureFlagOverride): void;

    public function delete(FlagKey $flagKey): void;
}
