<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Domain\Tenancy\Contract\Repository\TenantSettingsRepository;
use App\Domain\Tenancy\Contract\Service\TenantLogoStorage;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Eloquent\Tenancy\TenantPreferenceModel;
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
            name: TenantPreferenceModel::readDisplayName(),
            logoUrl: $this->resolveLogoUrl(TenantPreferenceModel::readLogoPath()),
            displayTimezone: TenantPreferenceModel::readDisplayTimezone(),
        );
    }

    public function updateSettings(
        string $tenantId,
        string $name,
        ?SplFileInfo $logo,
        bool $removeLogo,
        ?string $displayTimezone,
    ): void {
        TenantModel::findOrFail($tenantId);

        $currentLogoPath = TenantPreferenceModel::readLogoPath();

        $newLogoPath = $currentLogoPath;

        if ($removeLogo && $currentLogoPath !== null) {
            $this->tenantLogoStorage->delete($currentLogoPath);
            $newLogoPath = null;
        }

        if ($logo instanceof SplFileInfo) {
            if ($currentLogoPath !== null) {
                $this->tenantLogoStorage->delete($currentLogoPath);
            }

            $extension = $logo instanceof UploadedFile
                ? $logo->getClientOriginalExtension()
                : $logo->getExtension();
            $newLogoPath = $this->tenantLogoStorage->store(
                $tenantId,
                $logo->getPathname(),
                $extension,
            );
        }

        TenantPreferenceModel::writePreferences([
            'display_name' => $name,
            'logo_path' => $newLogoPath,
            'display_timezone' => $displayTimezone,
        ]);
    }

    private function resolveLogoUrl(?string $logoPath): ?string
    {
        if ($logoPath === null || ! $this->tenantLogoStorage->exists($logoPath)) {
            return null;
        }

        return $this->tenantLogoStorage->url($logoPath);
    }
}
