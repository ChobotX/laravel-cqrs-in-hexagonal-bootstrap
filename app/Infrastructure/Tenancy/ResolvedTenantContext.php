<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Contract\Tenancy\TenantContext;

final class ResolvedTenantContext implements TenantContext
{
    private ?string $tenantId = null;

    private ?string $tenantSlug = null;

    public function set(string $tenantId, string $tenantSlug): void
    {
        $this->tenantId = $tenantId;
        $this->tenantSlug = $tenantSlug;
    }

    public function currentTenantId(): string
    {
        return $this->tenantId ?? throw new TenantNotResolvedException;
    }

    public function currentTenantSlug(): string
    {
        return $this->tenantSlug ?? throw new TenantNotResolvedException;
    }

    public function isResolved(): bool
    {
        return $this->tenantId !== null;
    }
}
