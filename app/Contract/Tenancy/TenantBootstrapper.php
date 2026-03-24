<?php

declare(strict_types=1);

namespace App\Contract\Tenancy;

interface TenantBootstrapper
{
    public function bootstrapByDomain(string $domain): void;

    public function bootstrapBySlug(string $slug): void;

    public function reset(): void;
}
