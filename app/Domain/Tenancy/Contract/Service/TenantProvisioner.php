<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

/**
 * Domain service contract for tenant provisioner in the Tenancy bounded context.
 */
interface TenantProvisioner
{
    /** Persists a new or updated aggregate row. */
    public function createTenant(string $name, string $slug, ?string $domain): void;

    /** Contract operation `migrateTenant`; see infrastructure for behavior. */
    public function migrateTenant(string $slug): void;

    /** Contract operation `migrateAllTenants`; see infrastructure for behavior. */
    public function migrateAllTenants(): void;

    /** Clears tenant DB search_path scope (e.g. after CLI migrate). */
    public function resetTenantPersistenceScope(): void;
}
