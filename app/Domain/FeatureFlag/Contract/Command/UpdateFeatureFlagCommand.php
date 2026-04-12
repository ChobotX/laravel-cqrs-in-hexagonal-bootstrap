<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for update feature flag in the FeatureFlag bounded context; dispatched through the command bus.
 */
#[RequiresPermission('feature_flags.management.update')]
final readonly class UpdateFeatureFlagCommand implements Command
{
    public function __construct(
        /** Field `key` for this contract; see module docs for validation rules. */
        public string $key,
        /** Field `enabled` for this contract; see module docs for validation rules. */
        public bool $enabled,
        /** Optional `value`; null means not provided or not applicable. */
        public ?string $value = null,
    ) {}
}
