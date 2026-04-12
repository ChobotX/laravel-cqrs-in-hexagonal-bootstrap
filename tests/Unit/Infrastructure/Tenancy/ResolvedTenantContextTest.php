<?php

declare(strict_types=1);

use App\Infrastructure\Tenancy\ResolvedTenantContext;
use App\Infrastructure\Tenancy\TenantNotResolvedException;

it('returns tenant id after set', function (): void {
    $context = new ResolvedTenantContext;
    $context->set('tenant-123', 'test-slug', 'Test Tenant', '/storage/logo.png');

    expect($context->currentTenantId())->toBe('tenant-123')
        ->and($context->currentTenantSlug())->toBe('test-slug')
        ->and($context->currentTenantName())->toBe('Test Tenant')
        ->and($context->currentTenantLogoUrl())->toBe('/storage/logo.png')
        ->and($context->isResolved())->toBeTrue();
});

it('returns null logo url when none set', function (): void {
    $context = new ResolvedTenantContext;
    $context->set('tenant-123', 'test-slug', 'Test Tenant', null);

    expect($context->currentTenantLogoUrl())->toBeNull();
});

it('returns null display timezone when none set', function (): void {
    $context = new ResolvedTenantContext;
    $context->set('tenant-123', 'test-slug', 'Test Tenant', null);

    expect($context->currentTenantDisplayTimezone())->toBeNull();
});

it('returns display timezone when set', function (): void {
    $context = new ResolvedTenantContext;
    $context->set('tenant-123', 'test-slug', 'Test Tenant', null, 'Europe/Prague');

    expect($context->currentTenantDisplayTimezone())->toBe('Europe/Prague');
});

it('is not resolved by default', function (): void {
    $context = new ResolvedTenantContext;

    expect($context->isResolved())->toBeFalse();
});

it('throws when accessing tenant id before resolution', function (): void {
    $context = new ResolvedTenantContext;
    $context->currentTenantId();
})->throws(TenantNotResolvedException::class);

it('throws when accessing tenant slug before resolution', function (): void {
    $context = new ResolvedTenantContext;
    $context->currentTenantSlug();
})->throws(TenantNotResolvedException::class);

it('throws when accessing tenant name before resolution', function (): void {
    $context = new ResolvedTenantContext;
    $context->currentTenantName();
})->throws(TenantNotResolvedException::class);

it('throws when accessing tenant logo url before resolution', function (): void {
    $context = new ResolvedTenantContext;
    $context->currentTenantLogoUrl();
})->throws(TenantNotResolvedException::class);

it('throws when accessing tenant display timezone before resolution', function (): void {
    $context = new ResolvedTenantContext;
    $context->currentTenantDisplayTimezone();
})->throws(TenantNotResolvedException::class);
