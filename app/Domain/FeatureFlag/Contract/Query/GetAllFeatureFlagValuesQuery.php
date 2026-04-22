<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * Query for get all feature flag values in the FeatureFlag bounded context; dispatched through the query bus.
 *
 * @implements Query<array<string, array{enabled: bool, value: string}>>
 */
#[SkipPermissionCheck(reason: 'Feature flag values needed by all authenticated users for UI rendering')]
final readonly class GetAllFeatureFlagValuesQuery implements Query {}
