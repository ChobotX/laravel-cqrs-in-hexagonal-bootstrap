<?php

declare(strict_types=1);

use App\Contract\Auth\AccessDecision;
use App\Contract\Auth\AuthorizationChecker;
use App\Infrastructure\Authorization\CachedAuthorizationChecker;
use App\Infrastructure\Authorization\SimpleAccessDecision;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use Tests\Helper\FakeTenantContext;

/** @return array{CachedAuthorizationChecker, CachedCheckerTestInner, CacheRepository} */
function cachedCheckerSetup(bool $canResult = true, string $tenantSlug = 'acme'): array
{
    $cache = new CacheRepository(new ArrayStore);
    $inner = new CachedCheckerTestInner($canResult);
    $tenantContext = new FakeTenantContext('tenant-1', $tenantSlug);
    $checker = new CachedAuthorizationChecker($inner, $cache, $tenantContext, 300);

    return [$checker, $inner, $cache];
}

it('delegates to inner checker on cache miss and caches the result', function (): void {
    [$checker, $inner] = cachedCheckerSetup(true);

    expect($checker->can('user-1', 'users.list.read'))->toBeTrue();
    expect($inner->canCallCount)->toBe(1);

    expect($checker->can('user-1', 'users.list.read'))->toBeTrue();
    expect($inner->canCallCount)->toBe(1);
});

it('returns cached result on cache hit', function (): void {
    [$checker, $inner, $cache] = cachedCheckerSetup();

    $cache->put('acme:auth:can:user-1:users.list.read:v0', true, 300);

    expect($checker->can('user-1', 'users.list.read'))->toBeTrue();
    expect($inner->canCallCount)->toBe(0);
});

it('caches canWithScope result and returns cached on second call', function (): void {
    [$checker, $inner] = cachedCheckerSetup(true);

    $accessDecision = $checker->canWithScope('user-1', 'users.list.read');
    expect($accessDecision->granted())->toBeTrue()
        ->and($accessDecision->scope())->toBe('all');
    expect($inner->scopeCallCount)->toBe(1);

    $decision2 = $checker->canWithScope('user-1', 'users.list.read');
    expect($decision2->granted())->toBeTrue()
        ->and($decision2->scope())->toBe('all');
    expect($inner->scopeCallCount)->toBe(1);
});

it('caches accessibleResourceIds result', function (): void {
    [$checker, $inner] = cachedCheckerSetup();

    expect($checker->accessibleResourceIds('user-1', 'document', 'read'))->toBe([]);
    expect($inner->accessibleCallCount)->toBe(1);

    expect($checker->accessibleResourceIds('user-1', 'document', 'read'))->toBe([]);
    expect($inner->accessibleCallCount)->toBe(1);
});

it('caches denied permission result', function (): void {
    [$checker, $inner] = cachedCheckerSetup(false);

    expect($checker->can('user-1', 'users.list.delete'))->toBeFalse();
    expect($inner->canCallCount)->toBe(1);

    expect($checker->can('user-1', 'users.list.delete'))->toBeFalse();
    expect($inner->canCallCount)->toBe(1);
});

it('uses tenant-prefixed cache keys', function (): void {
    [$checker, $inner, $cache] = cachedCheckerSetup(true, 'tenant-a');

    $checker->can('user-1', 'users.list.read');

    expect($cache->has('tenant-a:auth:can:user-1:users.list.read:v0'))->toBeTrue();
});

it('isolates cache between tenants', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $inner = new CachedCheckerTestInner(true);

    $checkerA = new CachedAuthorizationChecker($inner, $cache, new FakeTenantContext('t1', 'tenant-a'), 300);
    $checkerB = new CachedAuthorizationChecker($inner, $cache, new FakeTenantContext('t2', 'tenant-b'), 300);

    $checkerA->can('user-1', 'users.list.read');
    expect($inner->canCallCount)->toBe(1);

    $checkerB->can('user-1', 'users.list.read');
    expect($inner->canCallCount)->toBe(2);
});

it('invalidates cache when version increments', function (): void {
    [$checker, $inner, $cache] = cachedCheckerSetup(true, 'acme');

    $checker->can('user-1', 'perm.a');
    expect($inner->canCallCount)->toBe(1);

    $cache->increment('acme:auth:version:user-1');

    $checker->can('user-1', 'perm.a');
    expect($inner->canCallCount)->toBe(2);
});

it('delegates supportsResourceSharing to inner checker', function (): void {
    [$checker] = cachedCheckerSetup();

    expect($checker->supportsResourceSharing('entry'))->toBeFalse();
});

it('delegates canShareResource to inner checker', function (): void {
    [$checker] = cachedCheckerSetup();

    expect($checker->canShareResource('user-1', 'entry'))->toBeFalse();
});

it('delegates canViewResourceShares to inner checker', function (): void {
    [$checker] = cachedCheckerSetup();

    expect($checker->canViewResourceShares('user-1', 'entry'))->toBeFalse();
});

it('delegates supportsResourceSharing and returns true when inner returns true', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $inner = new CachedCheckerTestInnerSupportingSharing;
    $tenantContext = new FakeTenantContext('tenant-1', 'acme');
    $checker = new CachedAuthorizationChecker($inner, $cache, $tenantContext, 300);

    expect($checker->supportsResourceSharing('entry'))->toBeTrue();
});

it('delegates canShareResource and returns true when inner returns true', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $inner = new CachedCheckerTestInnerSupportingSharing;
    $tenantContext = new FakeTenantContext('tenant-1', 'acme');
    $checker = new CachedAuthorizationChecker($inner, $cache, $tenantContext, 300);

    expect($checker->canShareResource('user-1', 'entry'))->toBeTrue();
});

it('delegates canViewResourceShares and returns true when inner returns true', function (): void {
    $cache = new CacheRepository(new ArrayStore);
    $inner = new CachedCheckerTestInnerSupportingSharing;
    $tenantContext = new FakeTenantContext('tenant-1', 'acme');
    $checker = new CachedAuthorizationChecker($inner, $cache, $tenantContext, 300);

    expect($checker->canViewResourceShares('user-1', 'entry'))->toBeTrue();
});

final class CachedCheckerTestInner implements AuthorizationChecker
{
    public int $canCallCount = 0;

    public int $scopeCallCount = 0;

    public int $accessibleCallCount = 0;

    public function __construct(private readonly bool $canResult = true) {}

    public function can(string $userId, string $permission): bool
    {
        $this->canCallCount++;

        return $this->canResult;
    }

    public function canWithScope(string $userId, string $permission): AccessDecision
    {
        $this->scopeCallCount++;

        return new SimpleAccessDecision($this->canResult, 'all');
    }

    /** @return list<string> */
    public function accessibleResourceIds(string $userId, string $resourceType, string $action): array
    {
        $this->accessibleCallCount++;

        return [];
    }

    public function supportsResourceSharing(string $resourceType): bool
    {
        return false;
    }

    public function canShareResource(string $userId, string $resourceType): bool
    {
        return false;
    }

    public function canViewResourceShares(string $userId, string $resourceType): bool
    {
        return false;
    }
}

final class CachedCheckerTestInnerSupportingSharing implements AuthorizationChecker
{
    public function can(string $userId, string $permission): bool
    {
        return false;
    }

    public function canWithScope(string $userId, string $permission): AccessDecision
    {
        return new SimpleAccessDecision(false, 'all');
    }

    /** @return list<string> */
    public function accessibleResourceIds(string $userId, string $resourceType, string $action): array
    {
        return [];
    }

    public function supportsResourceSharing(string $resourceType): bool
    {
        return true;
    }

    public function canShareResource(string $userId, string $resourceType): bool
    {
        return true;
    }

    public function canViewResourceShares(string $userId, string $resourceType): bool
    {
        return true;
    }
}
