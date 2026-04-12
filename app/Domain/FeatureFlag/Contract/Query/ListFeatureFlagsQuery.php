<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;

/**
 * Query for list feature flags in the FeatureFlag bounded context; dispatched through the query bus.
 *
 * @implements Query<list<ResolvedFlag>>
 */
#[RequiresPermission('feature_flags.management.read')]
final readonly class ListFeatureFlagsQuery implements Query {}
