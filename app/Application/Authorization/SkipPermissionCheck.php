<?php

declare(strict_types=1);

namespace App\Application\Authorization;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class SkipPermissionCheck
{
    public function __construct(
        public string $reason,
    ) {}
}
