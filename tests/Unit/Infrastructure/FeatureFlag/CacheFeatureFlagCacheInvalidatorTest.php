<?php

declare(strict_types=1);

use App\Infrastructure\FeatureFlag\CacheFeatureFlagCacheInvalidator;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use Tests\Helper\FakeTenantContext;

it('forgets tenant cache key', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $cache->put('feature-flags:tenant-1', ['billing.stripe' => '1'], 300);

    $invalidator = new CacheFeatureFlagCacheInvalidator($cache, new FakeTenantContext('tenant-1'));
    $invalidator->invalidate();

    expect($cache->has('feature-flags:tenant-1'))->toBeFalse();
});

it('does nothing when tenant is not resolved', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $cache->put('feature-flags:tenant-1', ['billing.stripe' => '1'], 300);

    $invalidator = new CacheFeatureFlagCacheInvalidator($cache, new FakeTenantContext);
    $invalidator->invalidate();

    expect($cache->has('feature-flags:tenant-1'))->toBeTrue();
});
