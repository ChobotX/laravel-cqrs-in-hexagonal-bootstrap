<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Query\GetTenantSettingsQuery;
use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;
use App\Domain\Tenancy\Exception\TenantNotFoundException;
use App\Domain\Tenancy\Query\GetTenantSettings\GetTenantSettingsHandler;
use Tests\Helper\FakeTenantSettingsRepository;

it('returns tenant settings when found', function (): void {
    $settings = new TenantSettings(name: 'Acme Corp', logoUrl: '/storage/tenant-logos/abc.png');
    $repo = new FakeTenantSettingsRepository(['tenant-1' => $settings]);
    $handler = new GetTenantSettingsHandler($repo);

    $tenantSettings = $handler->handle(new GetTenantSettingsQuery(tenantId: 'tenant-1'));

    expect($tenantSettings->name)->toBe('Acme Corp')
        ->and($tenantSettings->logoUrl)->toBe('/storage/tenant-logos/abc.png');
});

it('throws when tenant not found', function (): void {
    $repo = new FakeTenantSettingsRepository;
    $handler = new GetTenantSettingsHandler($repo);

    $handler->handle(new GetTenantSettingsQuery(tenantId: 'nonexistent'));
})->throws(TenantNotFoundException::class);
