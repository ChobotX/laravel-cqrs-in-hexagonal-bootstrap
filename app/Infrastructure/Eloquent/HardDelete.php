<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class HardDelete
{
    public function __construct(
        public string $reason,
    ) {}
}
