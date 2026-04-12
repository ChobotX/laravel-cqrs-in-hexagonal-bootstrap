<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Contract\Tenancy\TenantLogoStorage;
use App\Domain\Tenancy\Contract\Repository\TenantSettingsRepository;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Illuminate\Http\UploadedFile;
use SplFileInfo;

final readonly class EloquentTenantSettingsRepository implements TenantSettingsRepository
{
    public function __construct(
        private TenantLogoStorage $tenantLogoStorage,
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
            displayTimezone: $tenant->display_timezone,
        );
    }

    public function updateSettings(
        string $tenantId,
        string $name,
        ?SplFileInfo $logo,
        bool $removeLogo,
        ?string $displayTimezone,
    ): void {
        $tenant = TenantModel::findOrFail($tenantId);

        if ($removeLogo && $tenant->logo_path !== null) {
            $this->tenantLogoStorage->delete($tenant->logo_path);
            $tenant->logo_path = null;
        }

        if ($logo instanceof SplFileInfo) {
            if ($tenant->logo_path !== null) {
                $this->tenantLogoStorage->delete($tenant->logo_path);
            }

            $extension = $logo instanceof UploadedFile
                ? $logo->getClientOriginalExtension()
                : $logo->getExtension();
            $tenant->logo_path = $this->tenantLogoStorage->store(
                $tenantId,
                $logo->getPathname(),
                $extension,
            );
        }

        $tenant->name = $name;
        $tenant->display_timezone = $displayTimezone;
        $tenant->save();
    }

    private function resolveLogoUrl(?string $logoPath): ?string
    {
        if ($logoPath === null || ! $this->tenantLogoStorage->exists($logoPath)) {
            return null;
        }

        return $this->tenantLogoStorage->url($logoPath);
    }
}
