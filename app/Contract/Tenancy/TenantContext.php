<?php

declare(strict_types=1);

namespace App\Contract\Tenancy;

/**
 * Read-only view of the tenant bound to the current request after bootstrap (ids, branding, resolution state).
 */
interface TenantContext
{
    /** Primary tenant identifier used in persistence and authorization. */
    public function currentTenantId(): string;

    /** URL-safe tenant key. */
    public function currentTenantSlug(): string;

    /** Display name for UI and emails. */
    public function currentTenantName(): string;

    /** Public URL for tenant logo, or null when no logo is configured. */
    public function currentTenantLogoUrl(): ?string;

    /**
     * IANA timezone for rendering instants in the UI (e.g. Europe/Prague).
     * Null means use the browser's local timezone for display.
     */
    public function currentTenantDisplayTimezone(): ?string;

    /** Whether a tenant has been successfully resolved for this request. */
    public function isResolved(): bool;
}
