<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Query;

use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\FeatureFlag\Contract\ValueObject\FeatureFlagsGridResult;

/**
 * Composed read-model query for the feature flags grid. Handler dispatches
 * ListFeatureFlagsQuery, applies group filter / free-text search / sort /
 * pagination, and returns a pre-projected result with actor capabilities.
 *
 * @implements Query<FeatureFlagsGridResult>
 */
#[RequiresPermission('feature_flags.management.read')]
final readonly class ListFeatureFlagsGridQuery implements Query
{
    public function __construct(
        public Pagination $pagination,
        public ?Sorting $sorting,
        public string $search,
        public string $groupFilter,
        public string $actingUserId,
    ) {}
}
