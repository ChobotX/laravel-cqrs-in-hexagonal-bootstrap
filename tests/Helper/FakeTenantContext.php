<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Tenancy\TenantContext;
use RuntimeException;

final readonly class FakeTenantContext implements TenantContext
{
    public function __construct(
        private ?string $tenantId = null,
        private ?string $tenantSlug = null,
    ) {}

    public function currentTenantId(): string
    {
        if ($this->tenantId === null) {
            throw new RuntimeException('Tenant not resolved');
        }

        return $this->tenantId;
    }

    public function currentTenantSlug(): string
    {
        if ($this->tenantSlug === null) {
            throw new RuntimeException('Tenant not resolved');
        }

        return $this->tenantSlug;
    }

    public function isResolved(): bool
    {
        return $this->tenantId !== null;
    }
}
