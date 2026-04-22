<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for reset feature flag in the FeatureFlag bounded context; dispatched through the command bus.
 */
#[RequiresPermission('feature_flags.management.update')]
final readonly class ResetFeatureFlagCommand implements Command
{
    public function __construct(
        /** Field `key` for this contract; see module docs for validation rules. */
        public string $key,
    ) {}
}
