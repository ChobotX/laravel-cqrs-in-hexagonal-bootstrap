<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class TenantResolver
{
    public function resolveByDomain(string $subdomain): TenantModel
    {
        $domainModel = TenantDomainModel::with('tenant')
            ->where('domain', $subdomain)
            ->first();

        if ($domainModel === null || ! $domainModel->tenant instanceof TenantModel) {
            throw new NotFoundHttpException(sprintf('Tenant not found for domain "%s".', $subdomain));
        }

        $tenant = $domainModel->tenant;

        if (! $tenant->is_active) {
            throw new NotFoundHttpException(sprintf('Tenant "%s" is inactive.', $subdomain));
        }

        return $tenant;
    }

    public function resolveBySlug(string $slug): TenantModel
    {
        $tenant = TenantModel::where('slug', $slug)->first();

        if (! $tenant instanceof TenantModel) {
            throw new NotFoundHttpException(sprintf('Tenant not found for slug "%s".', $slug));
        }

        if (! $tenant->is_active) {
            throw new NotFoundHttpException(sprintf('Tenant "%s" is inactive.', $slug));
        }

        return $tenant;
    }
}
