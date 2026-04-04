<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

interface TenantProvisioner
{
    public function createTenant(string $name, string $slug, ?string $domain): void;

    public function migrateTenant(string $slug): void;

    public function migrateAllTenants(): void;
}
