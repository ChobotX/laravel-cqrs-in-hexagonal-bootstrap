<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\FeatureFlag\Contract\ValueObject\ResolvedFlag;

/**
 * @implements Query<list<ResolvedFlag>>
 */
#[RequiresPermission('feature_flags.management.read')]
final readonly class ListFeatureFlagsQuery implements Query {}
