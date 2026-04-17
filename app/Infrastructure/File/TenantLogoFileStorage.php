<?php

declare(strict_types=1);

namespace App\Infrastructure\File;

use App\Domain\Tenancy\Contract\Exception\TenantLogoStorageException;
use App\Domain\Tenancy\Contract\Service\TenantLogoStorage;
use Illuminate\Contracts\Filesystem\Filesystem;

final readonly class TenantLogoFileStorage implements TenantLogoStorage
{
    private const string DIRECTORY = 'tenant-logos';

    public function __construct(
        private Filesystem $filesystem,
    ) {}

    public function store(string $tenantId, string $sourcePath, string $extension): string
    {
        $path = $this->filesystem->putFileAs(
            self::DIRECTORY,
            $sourcePath,
            $tenantId.'.'.$extension,
        );

        if ($path === false) {
            throw new TenantLogoStorageException($tenantId);
        }

        return $path;
    }

    public function delete(string $path): void
    {
        $this->filesystem->delete($path);
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function url(string $path): string
    {
        return $this->filesystem->url($path);
    }
}
