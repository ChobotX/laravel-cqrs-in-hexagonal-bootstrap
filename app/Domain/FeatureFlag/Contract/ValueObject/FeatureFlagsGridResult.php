<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\ValueObject;

/**
 * Composed feature-flags grid result: rows + pagination meta + actor capabilities.
 */
final readonly class FeatureFlagsGridResult
{
    /**
     * @param  list<FeatureFlagGridRow>  $rows
     */
    public function __construct(
        public array $rows,
        public int $total,
        public int $page,
        public int $perPage,
        public int $totalPages,
        public FeatureFlagsGridPermissions $permissions,
    ) {}
}
