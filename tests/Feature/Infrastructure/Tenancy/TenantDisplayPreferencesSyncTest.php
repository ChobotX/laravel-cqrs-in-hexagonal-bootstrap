<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Tenancy\TenantPreferenceModel;
use App\Infrastructure\Tenancy\TenantDisplayPreferencesSync;
use App\Infrastructure\Tenancy\TenantPreferencesSingletonMissingException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

it('throws when tenant preferences singleton is missing', function (): void {
    DB::connection('tenant')->table('tenant_preferences')->where('id', 1)->delete();

    $tenantDisplayPreferencesSync = app(TenantDisplayPreferencesSync::class);

    expect(fn () => $tenantDisplayPreferencesSync->sync(testTenantSlug(), null))
        ->toThrow(TenantPreferencesSingletonMissingException::class);
});

it('fills display name from slug headline when display name is empty', function (): void {
    DB::connection('tenant')->table('tenant_preferences')->where('id', 1)->update(['display_name' => '']);

    app(TenantDisplayPreferencesSync::class)->sync(testTenantSlug(), null);

    $displayName = TenantPreferenceModel::query()->whereKey(1)->value('display_name');

    expect($displayName)->toBe(Str::headline(testTenantSlug()));
});

it('writes explicit display name when provided', function (): void {
    app(TenantDisplayPreferencesSync::class)->sync(testTenantSlug(), 'Acme From Migrate');

    expect(TenantPreferenceModel::query()->whereKey(1)->value('display_name'))->toBe('Acme From Migrate');
});
