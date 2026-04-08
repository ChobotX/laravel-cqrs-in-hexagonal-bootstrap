<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\EloquentTenantSettingsRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function settingsRepo(): EloquentTenantSettingsRepository
{
    return new EloquentTenantSettingsRepository(
        filesystem: Storage::fake('public'),
    );
}

it('reads tenant settings by id', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $repo = settingsRepo();

    $settings = $repo->findByTenantId($tenant->id);

    expect($settings)->not->toBeNull()
        ->and($settings->name)->toBe('Test Tenant')
        ->and($settings->logoUrl)->toBeNull();
});

it('returns null for nonexistent tenant', function (): void {
    $repo = settingsRepo();

    expect($repo->findByTenantId('99999999-9999-9999-9999-999999999999'))->toBeNull();
});

it('updates tenant name', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $repo = settingsRepo();

    $repo->updateSettings($tenant->id, 'Updated Name', null, false);

    $tenant->refresh();
    expect($tenant->name)->toBe('Updated Name');
});

it('stores a logo file', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $disk = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository($disk);

    $file = UploadedFile::fake()->image('logo.png', 100, 100);

    $repo->updateSettings($tenant->id, 'Acme Corp', $file, false);

    $tenant->refresh();
    expect($tenant->logo_path)->not->toBeNull();
    $disk->assertExists($tenant->logo_path);
});

it('removes a logo', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $disk = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository($disk);

    $file = UploadedFile::fake()->image('logo.png', 100, 100);
    $repo->updateSettings($tenant->id, 'Acme', $file, false);

    $tenant->refresh();
    $oldPath = $tenant->logo_path;
    expect($oldPath)->not->toBeNull();

    $repo->updateSettings($tenant->id, 'Acme', null, true);

    $tenant->refresh();
    expect($tenant->logo_path)->toBeNull();
    $disk->assertMissing($oldPath);
});

it('returns logo URL after upload via findByTenantId', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $disk = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository($disk);

    $file = UploadedFile::fake()->image('logo.png', 100, 100);
    $repo->updateSettings($tenant->id, 'Acme', $file, false);

    $settings = $repo->findByTenantId($tenant->id);

    expect($settings)->not->toBeNull()
        ->and($settings->logoUrl)->not->toBeNull();
});

it('returns null logo URL when file missing on disk', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $disk = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository($disk);

    $tenant->logo_path = 'tenant-logos/nonexistent.png';
    $tenant->save();

    $settings = $repo->findByTenantId($tenant->id);

    expect($settings)->not->toBeNull()
        ->and($settings->logoUrl)->toBeNull();
});

it('replaces existing logo when new one uploaded with different extension', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $disk = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository($disk);

    $file1 = UploadedFile::fake()->image('logo1.png', 100, 100);
    $repo->updateSettings($tenant->id, 'Acme', $file1, false);

    $tenant->refresh();
    $oldPath = $tenant->logo_path;
    expect($oldPath)->toContain('.png');

    $file2 = UploadedFile::fake()->image('logo2.jpg', 200, 200);
    $repo->updateSettings($tenant->id, 'Acme', $file2, false);

    $tenant->refresh();
    expect($tenant->logo_path)->toContain('.jpg');
    $disk->assertMissing($oldPath);
    $disk->assertExists($tenant->logo_path);
});
