<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;

it('constructs with name and logo URL', function (): void {
    $settings = new TenantSettings(name: 'Acme Corp', logoUrl: '/storage/tenant-logos/abc.png');

    expect($settings->name)->toBe('Acme Corp')
        ->and($settings->logoUrl)->toBe('/storage/tenant-logos/abc.png');
});

it('constructs with null logo URL', function (): void {
    $settings = new TenantSettings(name: 'Acme Corp', logoUrl: null);

    expect($settings->name)->toBe('Acme Corp')
        ->and($settings->logoUrl)->toBeNull();
});
