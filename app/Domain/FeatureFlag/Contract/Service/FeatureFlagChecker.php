<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Service;

/**
 * Domain service contract for feature flag in the FeatureFlag bounded context.
 */
interface FeatureFlagChecker
{
    /** Evaluates the rule without mutating domain state. */
    public function isEnabled(string $key): bool;

    /** Contract operation `value`; see infrastructure for behavior. */
    public function value(string $key): string;

    /**
     * @return array<string, array{enabled: bool, value: string}>
     *                                                            Contract operation `all`; see infrastructure for behavior.
     */
    public function all(): array;
}
