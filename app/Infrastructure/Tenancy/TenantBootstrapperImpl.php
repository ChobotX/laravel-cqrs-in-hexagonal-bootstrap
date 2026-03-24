<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Contract\Tenancy\TenantBootstrapper;

final readonly class TenantBootstrapperImpl implements TenantBootstrapper
{
    public function __construct(
        private TenantResolver $tenantResolver,
        private TenantSchemaManager $tenantSchemaManager,
        private ResolvedTenantContext $resolvedTenantContext,
    ) {}

    public function bootstrapByDomain(string $domain): void
    {
        $tenantModel = $this->tenantResolver->resolveByDomain($domain);
        $this->resolvedTenantContext->set($tenantModel->id, $tenantModel->slug);
        $this->tenantSchemaManager->switchTo($tenantModel);
    }

    public function bootstrapBySlug(string $slug): void
    {
        $tenantModel = $this->tenantResolver->resolveBySlug($slug);
        $this->resolvedTenantContext->set($tenantModel->id, $tenantModel->slug);
        $this->tenantSchemaManager->switchTo($tenantModel);
    }

    public function reset(): void
    {
        $this->tenantSchemaManager->reset();
    }
}
