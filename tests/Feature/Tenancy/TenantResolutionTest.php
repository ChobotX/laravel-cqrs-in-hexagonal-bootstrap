<?php

declare(strict_types=1);

use App\Contract\Tenancy\TenantBootstrapper;
use App\Contract\Tenancy\TenantContext;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;

it('resolves tenant from subdomain', function (): void {
    $this->get('http://test.laravel-bootstrap.local/login')
        ->assertOk();
});

it('returns 404 for unknown subdomain', function (): void {
    $this->get('http://unknown.laravel-bootstrap.local/login')
        ->assertNotFound();
});

it('returns 404 for inactive tenant', function (): void {
    TenantModel::where('slug', 'test')->update(['is_active' => false]);

    $this->get('http://test.laravel-bootstrap.local/login')
        ->assertNotFound();
});

it('serves root domain routes without tenant', function (): void {
    $this->get('http://laravel-bootstrap.local/')
        ->assertOk();
});

it('resolves tenant via CLI bootstrapper', function (): void {
    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapBySlug('test');

    $tenantContext = app(TenantContext::class);

    expect($tenantContext->isResolved())->toBeTrue()
        ->and($tenantContext->currentTenantSlug())->toBe('test')
        ->and($tenantContext->currentTenantId())->toBe('00000000-0000-0000-0000-000000000001');
});

it('throws for unknown slug via CLI', function (): void {
    $tenantBootstrapper = app(TenantBootstrapper::class);
    $tenantBootstrapper->bootstrapBySlug('nonexistent');
})->throws(Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
