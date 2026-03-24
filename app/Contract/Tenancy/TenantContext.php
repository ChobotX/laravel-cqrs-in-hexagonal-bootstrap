<?php

declare(strict_types=1);

namespace App\Contract\Tenancy;

interface TenantContext
{
    public function currentTenantId(): string;

    public function currentTenantSlug(): string;

    public function isResolved(): bool;
}
