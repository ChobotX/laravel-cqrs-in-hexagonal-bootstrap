<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessDecision;
use App\Contract\Authorization\AuthorizationChecker;
use App\Infrastructure\Authorization\CachedAuthorizationChecker;
use App\Infrastructure\Authorization\SimpleAccessDecision;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;

/** @return array{CachedAuthorizationChecker, CachedCheckerTestInner, CacheRepository} */
function cachedCheckerSetup(bool $canResult = true): array
{
    $cache = new CacheRepository(new ArrayStore);
    $inner = new CachedCheckerTestInner($canResult);
    $checker = new CachedAuthorizationChecker($inner, $cache, 300);

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

    $cache->put('auth:perms:user-1', ['users.list.read' => true], 300);

    expect($checker->can('user-1', 'users.list.read'))->toBeTrue();
    expect($inner->canCallCount)->toBe(0);
});

it('delegates canWithScope directly to inner checker', function (): void {
    [$checker] = cachedCheckerSetup(true);

    $accessDecision = $checker->canWithScope('user-1', 'users.list.read');

    expect($accessDecision->granted())->toBeTrue();
    expect($accessDecision->scope())->toBe('all');
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

final class CachedCheckerTestInner implements AuthorizationChecker
{
    public int $canCallCount = 0;

    public int $accessibleCallCount = 0;

    public function __construct(private readonly bool $canResult = true) {}

    public function can(string $userId, string $permission): bool
    {
        $this->canCallCount++;

        return $this->canResult;
    }

    public function canWithScope(string $userId, string $permission): AccessDecision
    {
        return new SimpleAccessDecision($this->canResult, 'all');
    }

    /** @return list<string> */
    public function accessibleResourceIds(string $userId, string $resourceType, string $action): array
    {
        $this->accessibleCallCount++;

        return [];
    }
}
