<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Contract\Tenancy\TenantBootstrapper;
use App\Contract\Tenancy\TenantLogoStorage;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;

final readonly class TenantBootstrapperImpl implements TenantBootstrapper
{
    public function __construct(
        private TenantResolver $tenantResolver,
        private TenantSchemaManager $tenantSchemaManager,
        private ResolvedTenantContext $resolvedTenantContext,
        private TenantLogoStorage $tenantLogoStorage,
    ) {}

    public function bootstrapByDomain(string $domain): void
    {
        $tenantModel = $this->tenantResolver->resolveByDomain($domain);
        $this->bootstrap($tenantModel);
    }

    public function bootstrapBySlug(string $slug): void
    {
        $tenantModel = $this->tenantResolver->resolveBySlug($slug);
        $this->bootstrap($tenantModel);
    }

    public function reset(): void
    {
        $this->tenantSchemaManager->reset();
    }

    private function bootstrap(TenantModel $tenantModel): void
    {
        $this->resolvedTenantContext->set(
            $tenantModel->id,
            $tenantModel->slug,
            $tenantModel->name,
            $this->resolveLogoUrl($tenantModel->logo_path),
            $tenantModel->display_timezone,
        );
        $this->tenantSchemaManager->switchTo($tenantModel);
    }

    private function resolveLogoUrl(?string $logoPath): ?string
    {
        if ($logoPath === null || ! $this->tenantLogoStorage->exists($logoPath)) {
            return null;
        }

        return $this->tenantLogoStorage->url($logoPath);
    }
}
