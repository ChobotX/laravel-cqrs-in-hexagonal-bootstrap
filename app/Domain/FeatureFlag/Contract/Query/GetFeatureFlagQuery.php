<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;

/**
 * Query for get feature flag in the FeatureFlag bounded context; dispatched through the query bus.
 *
 * @implements Query<ResolvedFlag>
 */
#[RequiresPermission('feature_flags.management.read')]
final readonly class GetFeatureFlagQuery implements Query
{
    public function __construct(
        /** Field `key` for this contract; see module docs for validation rules. */
        public string $key,
    ) {}
}
