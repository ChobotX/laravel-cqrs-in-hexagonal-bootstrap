<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Contract\Tenancy\TenantBootstrapper;

final readonly class TenantBootstrapperImpl implements TenantBootstrapper
{
    public function __construct(
        private TenantResolver $tenantResolver,
        private TenantSchemaManager $schemaManager,
        private ResolvedTenantContext $tenantContext,
    ) {}

    public function bootstrapByDomain(string $domain): void
    {
        $tenant = $this->tenantResolver->resolveByDomain($domain);
        $this->tenantContext->set($tenant->id, $tenant->slug);
        $this->schemaManager->switchTo($tenant);
    }

    public function bootstrapBySlug(string $slug): void
    {
        $tenant = $this->tenantResolver->resolveBySlug($slug);
        $this->tenantContext->set($tenant->id, $tenant->slug);
        $this->schemaManager->switchTo($tenant);
    }

    public function reset(): void
    {
        $this->schemaManager->reset();
    }
}
