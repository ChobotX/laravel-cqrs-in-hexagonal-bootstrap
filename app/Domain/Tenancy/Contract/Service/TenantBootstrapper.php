<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Service;

/**
 * Resolves the current tenant from an HTTP host or slug and switches persistence (schema/connection) for the request.
 */
interface TenantBootstrapper
{
    /** Resolves tenant from request host or custom domain and applies tenant-scoped database context. */
    public function bootstrapByDomain(string $domain): void;

    /** Resolves tenant from URL or path slug when domain alone is insufficient. */
    public function bootstrapBySlug(string $slug): void;

    /** Clears tenant binding (e.g. console or tests) so subsequent work uses landlord or default connection. */
    public function reset(): void;
}
