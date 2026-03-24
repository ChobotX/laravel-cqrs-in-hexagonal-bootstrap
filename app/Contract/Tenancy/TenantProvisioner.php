<?php

declare(strict_types=1);

namespace App\Contract\Tenancy;

interface TenantProvisioner
{
    public function createTenant(string $name, string $slug, ?string $domain): void;

    public function migrateTenant(string $slug): void;

    public function migrateAllTenants(): void;
}
