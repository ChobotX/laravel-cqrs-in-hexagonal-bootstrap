<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Infrastructure\Eloquent\Tenancy\TenantPreferenceModel;
use Illuminate\Support\Str;

/**
 * Ensures tenant_preferences.display_name is populated after tenant migrations.
 */
final readonly class TenantDisplayPreferencesSync
{
    public function sync(string $slug, ?string $displayName): void
    {
        $preferences = TenantPreferenceModel::query()->whereKey(TenantPreferenceModel::SINGLETON_ID)->first([
            'display_name',
            'logo_path',
            'display_timezone',
        ]);

        if ($preferences === null) {
            throw new TenantPreferencesSingletonMissingException($slug);
        }

        $displayNameRaw = $preferences->getAttribute('display_name');
        $currentName = trim(is_string($displayNameRaw) ? $displayNameRaw : '');

        if ($displayName !== null) {
            $this->writeTenantDisplayPreferences($preferences, $displayName);

            return;
        }

        if ($currentName === '') {
            $this->writeTenantDisplayPreferences($preferences, Str::headline($slug));
        }
    }

    private function writeTenantDisplayPreferences(TenantPreferenceModel $tenantPreferenceModel, string $displayName): void
    {
        $logoPath = $tenantPreferenceModel->logo_path;
        $displayTimezone = $tenantPreferenceModel->display_timezone;

        TenantPreferenceModel::writePreferences([
            'display_name' => $displayName,
            'logo_path' => ($logoPath ?? '') !== '' ? $logoPath : null,
            'display_timezone' => ($displayTimezone ?? '') !== '' ? $displayTimezone : null,
        ]);
    }
}
