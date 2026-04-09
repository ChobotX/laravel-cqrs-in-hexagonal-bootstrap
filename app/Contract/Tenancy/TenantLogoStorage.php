<?php

declare(strict_types=1);

namespace App\Contract\Tenancy;

interface TenantLogoStorage
{
    public function store(string $tenantId, string $sourcePath, string $extension): string;

    public function delete(string $path): void;

    public function exists(string $path): bool;

    public function url(string $path): string;
}
