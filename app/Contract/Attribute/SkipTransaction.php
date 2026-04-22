<?php

declare(strict_types=1);

namespace App\Contract\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class SkipTransaction
{
    public function __construct(
        public string $reason,
    ) {}
}
