<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\ValueObject\TenantSettings;

it('constructs with name and logo URL', function (): void {
    $settings = new TenantSettings(name: 'Acme Corp', logoUrl: '/storage/tenant-logos/abc.png', displayTimezone: null);

    expect($settings->name)->toBe('Acme Corp')
        ->and($settings->logoUrl)->toBe('/storage/tenant-logos/abc.png')
        ->and($settings->displayTimezone)->toBeNull();
});

it('constructs with null logo URL', function (): void {
    $settings = new TenantSettings(name: 'Acme Corp', logoUrl: null, displayTimezone: 'Europe/Prague');

    expect($settings->name)->toBe('Acme Corp')
        ->and($settings->logoUrl)->toBeNull()
        ->and($settings->displayTimezone)->toBe('Europe/Prague');
});
