<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Repository;

use App\Domain\FeatureFlag\Contract\Entity\FeatureFlagOverride;
use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;

/**
 * Persistence port for feature flag override data in the FeatureFlag context; implementations live in Infrastructure.
 */
interface FeatureFlagOverrideRepository
{
    /**
     * @return list<FeatureFlagOverride>
     *                                   Loads a record or value object, or null when absent.
     */
    public function findAll(): array;

    /** Loads a record or value object, or null when absent. */
    public function findByKey(FlagKey $flagKey): ?FeatureFlagOverride;

    /** Persists a new or updated aggregate row. */
    public function save(FeatureFlagOverride $featureFlagOverride): void;

    /** Deletes or soft-deletes the targeted record. */
    public function delete(FlagKey $flagKey): void;
}
