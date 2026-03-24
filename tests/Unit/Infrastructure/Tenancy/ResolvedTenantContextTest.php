<?php

declare(strict_types=1);

use App\Infrastructure\Tenancy\ResolvedTenantContext;
use App\Infrastructure\Tenancy\TenantNotResolvedException;

it('returns tenant id after set', function (): void {
    $context = new ResolvedTenantContext;
    $context->set('tenant-123', 'test-slug');

    expect($context->currentTenantId())->toBe('tenant-123')
        ->and($context->currentTenantSlug())->toBe('test-slug')
        ->and($context->isResolved())->toBeTrue();
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
