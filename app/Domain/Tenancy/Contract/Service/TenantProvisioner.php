<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

/**
 * Domain service contract for tenant provisioner in the Tenancy bounded context.
 */
interface TenantProvisioner
{
    /** Persists landlord routing row (no display name — lives in tenant_preferences after migrate). */
    public function createTenant(string $slug, ?string $domain): void;

    /**
     * Runs tenant migrations and ensures tenant_preferences.display_name is set.
     *
     * @param  string|null  $displayName  When non-null, written as organization display name (create-tenant flow).
     */
    public function migrateTenant(string $slug, ?string $displayName = null): void;

    /** Contract operation `migrateAllTenants`; see infrastructure for behavior. */
    public function migrateAllTenants(): void;

    /** Clears tenant DB search_path scope (e.g. after CLI migrate). */
    public function resetTenantPersistenceScope(): void;
}
