<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\ValueObject;

final readonly class TenantSettings
{
    public function __construct(
        public string $name,
        public ?string $logoUrl,
    ) {}
}
