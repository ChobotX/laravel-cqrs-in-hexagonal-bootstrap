<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Domain\Tenancy\Contract\Exception\InactiveTenantException;
use App\Domain\Tenancy\Contract\Exception\TenantNotFoundException;
use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;

final readonly class TenantResolver
{
    public function resolveByDomain(string $subdomain): TenantModel
    {
        $domainModel = TenantDomainModel::with('tenant')
            ->where('domain', $subdomain)
            ->first();

        if ($domainModel === null || ! $domainModel->tenant instanceof TenantModel) {
            throw new TenantNotFoundException($subdomain);
        }

        $tenant = $domainModel->tenant;

        if (! $tenant->is_active) {
            throw new InactiveTenantException($subdomain);
        }

        return $tenant;
    }

    public function resolveBySlug(string $slug): TenantModel
    {
        $tenant = TenantModel::where('slug', $slug)->first();

        if (! $tenant instanceof TenantModel) {
            throw new TenantNotFoundException($slug);
        }

        if (! $tenant->is_active) {
            throw new InactiveTenantException($slug);
        }

        return $tenant;
    }
}
