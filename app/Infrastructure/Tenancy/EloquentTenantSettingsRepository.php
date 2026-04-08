<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Domain\Tenancy\TenantSettings;
use App\Domain\Tenancy\TenantSettingsRepository;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use SplFileInfo;

final readonly class EloquentTenantSettingsRepository implements TenantSettingsRepository
{
    public function __construct(
        private Filesystem $filesystem,
    ) {}

    public function findByTenantId(string $tenantId): ?TenantSettings
    {
        $tenant = TenantModel::find($tenantId);

        if (! $tenant instanceof TenantModel) {
            return null;
        }

        return new TenantSettings(
            name: $tenant->name,
            logoUrl: $this->resolveLogoUrl($tenant->logo_path),
        );
    }

    public function updateSettings(string $tenantId, string $name, ?SplFileInfo $logo, bool $removeLogo): void
    {
        $tenant = TenantModel::findOrFail($tenantId);

        if ($removeLogo && $tenant->logo_path !== null) {
            $this->filesystem->delete($tenant->logo_path);
            $tenant->logo_path = null;
        }

        if ($logo instanceof SplFileInfo) {
            if ($tenant->logo_path !== null) {
                $this->filesystem->delete($tenant->logo_path);
            }

            $extension = $logo instanceof UploadedFile
                ? $logo->getClientOriginalExtension()
                : $logo->getExtension();
            $tenant->logo_path = $this->filesystem->putFileAs(
                'tenant-logos',
                $logo->getPathname(),
                $tenantId.'.'.$extension,
            );
        }

        $tenant->name = $name;
        $tenant->save();
    }

    private function resolveLogoUrl(?string $logoPath): ?string
    {
        if ($logoPath === null || ! $this->filesystem->exists($logoPath)) {
            return null;
        }

        return $this->filesystem->url($logoPath);
    }
}
