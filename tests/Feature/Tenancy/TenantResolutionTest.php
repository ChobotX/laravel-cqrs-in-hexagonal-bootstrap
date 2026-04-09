<?php

declare(strict_types=1);

use App\Contract\Tenancy\TenantBootstrapper;
use App\Contract\Tenancy\TenantContext;
use App\Domain\Tenancy\Contract\Exception\InactiveTenantException;
use App\Domain\Tenancy\Contract\Exception\TenantNotFoundException;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Illuminate\Support\Facades\Storage;

it('resolves tenant from subdomain', function (): void {
    $this->get('http://'.testTenantDomain().'.laravel-bootstrap.local/login')
        ->assertOk();
});

it('returns 404 for unknown subdomain', function (): void {
    $this->get('http://unknown.laravel-bootstrap.local/login')
        ->assertNotFound();
});

it('returns 404 for inactive tenant', function (): void {
    TenantModel::where('slug', testTenantSlug())->update(['is_active' => false]);

    $this->get('http://'.testTenantDomain().'.laravel-bootstrap.local/login')
        ->assertNotFound();
});

it('serves root domain routes without tenant', function (): void {
    $this->get('http://laravel-bootstrap.local/')
        ->assertOk();
});

it('resolves tenant via CLI bootstrapper', function (): void {
    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapBySlug(testTenantSlug());

    $tenantContext = app(TenantContext::class);

    expect($tenantContext->isResolved())->toBeTrue()
        ->and($tenantContext->currentTenantSlug())->toBe(testTenantSlug());
});

it('throws for unknown slug via CLI', function (): void {
    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapBySlug('nonexistent');
})->throws(TenantNotFoundException::class);

it('resets tenant context via bootstrapper', function (): void {
    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapBySlug(testTenantSlug());
    $tenantBootstrapper->reset();

    expect(config('database.connections.tenant.search_path'))->toBe('public');
});

it('resolves tenant by domain via bootstrapper', function (): void {
    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapByDomain(testTenantDomain());

    $tenantContext = app(TenantContext::class);

    expect($tenantContext->isResolved())->toBeTrue()
        ->and($tenantContext->currentTenantSlug())->toBe(testTenantSlug());
});

it('throws for unknown domain via bootstrapper', function (): void {
    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapByDomain('nonexistent');
})->throws(TenantNotFoundException::class);

it('throws for inactive tenant domain', function (): void {
    TenantModel::where('slug', testTenantSlug())->update(['is_active' => false]);

    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapByDomain(testTenantDomain());
})->throws(InactiveTenantException::class);

it('resolves logo URL when tenant has a logo file', function (): void {
    $filesystem = Storage::fake('public');
    $filesystem->put('tenant-logos/test-logo.webp', 'fake-image');

    $tenant = TenantModel::findOrFail(test()->tenantId());
    $tenant->logo_path = 'tenant-logos/test-logo.webp';
    $tenant->save();

    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapBySlug(testTenantSlug());

    $tenantContext = app(TenantContext::class);
    expect($tenantContext->currentTenantLogoUrl())->not->toBeNull();
});

it('throws for inactive tenant slug', function (): void {
    TenantModel::where('slug', testTenantSlug())->update(['is_active' => false]);

    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapBySlug(testTenantSlug());
})->throws(InactiveTenantException::class);
