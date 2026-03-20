<?php

declare(strict_types=1);

namespace App\Application\Organization;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class AllowNullableOrganizationId
{
    public function __construct(
        public string $reason,
    ) {}
}
