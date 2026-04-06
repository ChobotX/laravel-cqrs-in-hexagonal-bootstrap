<?php

declare(strict_types=1);

namespace App\Domain\FeatureFlag\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('feature_flags.management.update')]
final readonly class UpdateFeatureFlagCommand implements Command
{
    public function __construct(
        public string $key,
        public bool $enabled,
        public ?string $value = null,
    ) {}
}
