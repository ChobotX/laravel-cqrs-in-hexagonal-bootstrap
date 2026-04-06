<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * @implements Query<array<string, array{enabled: bool, value: string}>>
 */
#[SkipPermissionCheck(reason: 'Feature flag values needed by all authenticated users for UI rendering')]
final readonly class GetAllFeatureFlagValuesQuery implements Query {}
