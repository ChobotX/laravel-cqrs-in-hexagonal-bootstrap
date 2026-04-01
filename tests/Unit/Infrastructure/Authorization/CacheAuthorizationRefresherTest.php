<?php

declare(strict_types=1);

use App\Infrastructure\Authorization\CacheAuthorizationRefresher;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use Tests\Helper\FakeTenantContext;

it('increments user auth version to invalidate all cached entries', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $tenantContext = new FakeTenantContext('tenant-1', 'acme');

    $refresher = new CacheAuthorizationRefresher($cache, $tenantContext);
    $refresher->refreshForUser('user-1');

    /** @var int $version */
    $version = $cache->get('acme:auth:version:user-1');
    expect($version)->toBe(1);

    $refresher->refreshForUser('user-1');

    /** @var int $version2 */
    $version2 = $cache->get('acme:auth:version:user-1');
    expect($version2)->toBe(2);
});

it('does not affect other users version', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $tenantContext = new FakeTenantContext('tenant-1', 'acme');

    $refresher = new CacheAuthorizationRefresher($cache, $tenantContext);
    $refresher->refreshForUser('user-1');
    $refresher->refreshForUser('user-1');

    /** @var int $v1 */
    $v1 = $cache->get('acme:auth:version:user-1', 0);
    /** @var int $v2 */
    $v2 = $cache->get('acme:auth:version:user-2', 0);

    expect($v1)->toBe(2)
        ->and($v2)->toBe(0);
});

it('uses tenant-prefixed version keys', function (): void {
    $cache = new CacheRepository(new ArrayStore);

    $refresherA = new CacheAuthorizationRefresher($cache, new FakeTenantContext('t1', 'tenant-a'));
    $refresherB = new CacheAuthorizationRefresher($cache, new FakeTenantContext('t2', 'tenant-b'));

    $refresherA->refreshForUser('user-1');

    /** @var int $va */
    $va = $cache->get('tenant-a:auth:version:user-1', 0);
    /** @var int $vb */
    $vb = $cache->get('tenant-b:auth:version:user-1', 0);

    expect($va)->toBe(1)
        ->and($vb)->toBe(0);
});
