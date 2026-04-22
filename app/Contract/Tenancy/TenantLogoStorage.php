<?php

declare(strict_types=1);

namespace App\Contract\Tenancy;

/**
 * Persists tenant logo files on disk or object storage and exposes public URLs for {@see TenantContext::currentTenantLogoUrl()}.
 */
interface TenantLogoStorage
{
    /**
     * Copies a local upload into tenant-scoped storage and returns the stored relative or logical path.
     */
    public function store(string $tenantId, string $sourcePath, string $extension): string;

    /** Removes a previously stored logo; no-op or safe failure if the path does not exist. */
    public function delete(string $path): void;

    /** Whether the object at `$path` is present in storage. */
    public function exists(string $path): bool;

    /** Absolute or root-relative URL clients may use in img src attributes. */
    public function url(string $path): string;
}
