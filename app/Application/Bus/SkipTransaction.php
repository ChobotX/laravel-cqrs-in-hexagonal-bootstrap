<?php

declare(strict_types=1);

namespace App\Application\Bus;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class SkipTransaction
{
    public function __construct(
        public string $reason,
    ) {}
}
