<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Eloquent\Tenancy\TenantPreferenceModel;
use App\Infrastructure\Filesystem\TenantLogoFileStorage;
use App\Infrastructure\Tenancy\EloquentTenantSettingsRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function settingsRepo(): EloquentTenantSettingsRepository
{
    return new EloquentTenantSettingsRepository(
        tenantLogoStorage: new TenantLogoFileStorage(Storage::fake('public')),
    );
}

it('reads tenant settings by id', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $eloquentTenantSettingsRepository = settingsRepo();

    $settings = $eloquentTenantSettingsRepository->findByTenantId($tenant->id);

    expect($settings)->not->toBeNull()
        ->and($settings->name)->toBe('Test Tenant')
        ->and($settings->logoUrl)->toBeNull()
        ->and($settings->displayTimezone)->toBeNull();
});

it('returns null for nonexistent tenant', function (): void {
    $eloquentTenantSettingsRepository = settingsRepo();

    expect($eloquentTenantSettingsRepository->findByTenantId('99999999-9999-9999-9999-999999999999'))->toBeNull();
});

it('updates tenant name', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $eloquentTenantSettingsRepository = settingsRepo();

    $eloquentTenantSettingsRepository->updateSettings($tenant->id, 'Updated Name', null, false, null);

    $tenant->refresh();
    expect($tenant->name)->toBe('Updated Name');
});

it('stores a logo file', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $filesystem = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository(new TenantLogoFileStorage($filesystem));

    $file = UploadedFile::fake()->image('logo.png', 100, 100);

    $repo->updateSettings($tenant->id, 'Acme Corp', $file, false, null);

    $tenant->refresh();
    expect($tenant->logo_path)->not->toBeNull();
    $filesystem->assertExists($tenant->logo_path);
});

it('removes a logo', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $filesystem = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository(new TenantLogoFileStorage($filesystem));

    $file = UploadedFile::fake()->image('logo.png', 100, 100);
    $repo->updateSettings($tenant->id, 'Acme', $file, false, null);

    $tenant->refresh();
    $oldPath = $tenant->logo_path;
    expect($oldPath)->not->toBeNull();

    $repo->updateSettings($tenant->id, 'Acme', null, true, null);

    $tenant->refresh();
    expect($tenant->logo_path)->toBeNull();
    $filesystem->assertMissing($oldPath);
});

it('returns logo URL after upload via findByTenantId', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $filesystem = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository(new TenantLogoFileStorage($filesystem));

    $file = UploadedFile::fake()->image('logo.png', 100, 100);
    $repo->updateSettings($tenant->id, 'Acme', $file, false, null);

    $settings = $repo->findByTenantId($tenant->id);

    expect($settings)->not->toBeNull()
        ->and($settings->logoUrl)->not->toBeNull();
});

it('returns null logo URL when file missing on disk', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $filesystem = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository(new TenantLogoFileStorage($filesystem));

    $tenant->logo_path = 'tenant-logos/nonexistent.png';
    $tenant->save();

    $settings = $repo->findByTenantId($tenant->id);

    expect($settings)->not->toBeNull()
        ->and($settings->logoUrl)->toBeNull();
});

it('stores logo from SplFileInfo using file extension', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $filesystem = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository(new TenantLogoFileStorage($filesystem));

    $tempPath = tempnam(sys_get_temp_dir(), 'logo_').'.webp';
    $img = imagecreatetruecolor(10, 10);
    imagewebp($img, $tempPath);
    imagedestroy($img);

    $spl = new SplFileInfo($tempPath);
    $repo->updateSettings($tenant->id, 'Acme', $spl, false, null);

    $tenant->refresh();
    expect($tenant->logo_path)->toContain('.webp');
    $filesystem->assertExists($tenant->logo_path);

    unlink($tempPath);
});

it('replaces existing logo when new one uploaded with different extension', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $filesystem = Storage::fake('public');
    $repo = new EloquentTenantSettingsRepository(new TenantLogoFileStorage($filesystem));

    $file1 = UploadedFile::fake()->image('logo1.png', 100, 100);
    $repo->updateSettings($tenant->id, 'Acme', $file1, false, null);

    $tenant->refresh();
    $oldPath = $tenant->logo_path;
    expect($oldPath)->toContain('.png');

    $file2 = UploadedFile::fake()->image('logo2.jpg', 200, 200);
    $repo->updateSettings($tenant->id, 'Acme', $file2, false, null);

    $tenant->refresh();
    expect($tenant->logo_path)->toContain('.jpg');
    $filesystem->assertMissing($oldPath);
    $filesystem->assertExists($tenant->logo_path);
});

it('persists display timezone', function (): void {
    $tenant = TenantModel::findOrFail(test()->tenantId());
    $eloquentTenantSettingsRepository = settingsRepo();

    $eloquentTenantSettingsRepository->updateSettings($tenant->id, 'Acme', null, false, 'Europe/Prague');

    $tenant->refresh();
    expect(TenantPreferenceModel::readDisplayTimezone())->toBe('Europe/Prague');

    $read = $eloquentTenantSettingsRepository->findByTenantId($tenant->id);
    expect($read)->not->toBeNull()
        ->and($read->displayTimezone)->toBe('Europe/Prague');
});
