<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Service;

use App\Domain\FeatureFlag\Contract\ValueObject\FlagDefinition;

/**
 * Domain service contract for feature flag definition in the FeatureFlag bounded context.
 */
interface FeatureFlagDefinitionProvider
{
    /**
     * @return list<FlagDefinition>
     *                              Contract operation `all`; see infrastructure for behavior.
     */
    public function all(): array;

    /** Loads a record or value object, or null when absent. */
    public function get(string $key): ?FlagDefinition;
}
