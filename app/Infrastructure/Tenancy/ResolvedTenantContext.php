<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Domain\Tenancy\Contract\Service\TenantContext;

final class ResolvedTenantContext implements TenantContext
{
    private ?string $tenantId = null;

    private ?string $tenantSlug = null;

    private ?string $tenantName = null;

    private ?string $tenantLogoUrl = null;

    private ?string $tenantDisplayTimezone = null;

    public function set(
        string $tenantId,
        string $tenantSlug,
        string $tenantName,
        ?string $tenantLogoUrl,
        ?string $tenantDisplayTimezone = null,
    ): void {
        $this->tenantId = $tenantId;
        $this->tenantSlug = $tenantSlug;
        $this->tenantName = $tenantName;
        $this->tenantLogoUrl = $tenantLogoUrl;
        $this->tenantDisplayTimezone = $tenantDisplayTimezone;
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

    public function currentTenantDisplayTimezone(): ?string
    {
        if (! $this->isResolved()) {
            throw new TenantNotResolvedException;
        }

        return $this->tenantDisplayTimezone;
    }

    public function isResolved(): bool
    {
        return $this->tenantId !== null;
    }
}
