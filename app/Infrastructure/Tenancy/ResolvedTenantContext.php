<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Contract\Tenancy\TenantContext;

final class ResolvedTenantContext implements TenantContext
{
    private ?string $tenantId = null;

    private ?string $tenantSlug = null;

    private ?string $tenantName = null;

    private ?string $tenantLogoUrl = null;

    public function set(string $tenantId, string $tenantSlug, string $tenantName, ?string $tenantLogoUrl): void
    {
        $this->tenantId = $tenantId;
        $this->tenantSlug = $tenantSlug;
        $this->tenantName = $tenantName;
        $this->tenantLogoUrl = $tenantLogoUrl;
    }

    public function currentTenantId(): string
    {
        return $this->tenantId ?? throw new TenantNotResolvedException;
    }

    public function currentTenantSlug(): string
    {
        return $this->tenantSlug ?? throw new TenantNotResolvedException;
    }

    public function currentTenantName(): string
    {
        return $this->tenantName ?? throw new TenantNotResolvedException;
    }

    public function currentTenantLogoUrl(): ?string
    {
        if (! $this->isResolved()) {
            throw new TenantNotResolvedException;
        }

        return $this->tenantLogoUrl;
    }

    public function isResolved(): bool
    {
        return $this->tenantId !== null;
    }
}
