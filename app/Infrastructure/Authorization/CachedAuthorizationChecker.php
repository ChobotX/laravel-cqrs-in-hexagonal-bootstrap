<?php

declare(strict_types=1);

namespace App\Infrastructure\Authorization;

use App\Contract\Auth\AccessDecision;
use App\Contract\Auth\AuthorizationChecker;
use App\Contract\Tenancy\TenantContext;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class CachedAuthorizationChecker implements AuthorizationChecker
{
    public function __construct(
        private AuthorizationChecker $authorizationChecker,
        private CacheRepository $cacheRepository,
        private TenantContext $tenantContext,
        private int $ttl,
    ) {}

    public function can(string $userId, string $permission): bool
    {
        /** @var bool $result */
        $result = $this->cacheRepository->remember(
            $this->canCacheKey($userId, $permission),
            $this->ttl,
            fn (): bool => $this->authorizationChecker->can($userId, $permission),
        );

        return $result;
    }

    public function canWithScope(string $userId, string $permission): AccessDecision
    {
        $key = $this->scopeCacheKey($userId, $permission);

        /** @var array{granted: bool, scope: string} $cached */
        $cached = $this->cacheRepository->remember(
            $key,
            $this->ttl,
            function () use ($userId, $permission): array {
                $accessDecision = $this->authorizationChecker->canWithScope($userId, $permission);

                return ['granted' => $accessDecision->granted(), 'scope' => $accessDecision->scope()];
            },
        );

        return new SimpleAccessDecision($cached['granted'], $cached['scope']);
    }

    /** @return list<string> */
    public function accessibleResourceIds(
        string $userId,
        string $resourceType,
        string $action,
    ): array {
        $key = $this->sharesCacheKey($userId, $resourceType, $action);

        /* @var list<string> */
        return $this->cacheRepository->remember(
            $key,
            $this->ttl,
            fn (): array => $this->authorizationChecker->accessibleResourceIds($userId, $resourceType, $action),
        );
    }

    public function supportsResourceSharing(string $resourceType): bool
    {
        return $this->authorizationChecker->supportsResourceSharing($resourceType);
    }

    public function canShareResource(string $userId, string $resourceType): bool
    {
        return $this->authorizationChecker->canShareResource($userId, $resourceType);
    }

    public function canViewResourceShares(string $userId, string $resourceType): bool
    {
        return $this->authorizationChecker->canViewResourceShares($userId, $resourceType);
    }

    private function canCacheKey(string $userId, string $permission): string
    {
        return sprintf('%s:auth:can:%s:%s:v%d', $this->tenantPrefix(), $userId, $permission, $this->authVersion($userId));
    }

    private function scopeCacheKey(string $userId, string $permission): string
    {
        return sprintf('%s:auth:scope:%s:%s:v%d', $this->tenantPrefix(), $userId, $permission, $this->authVersion($userId));
    }

    private function sharesCacheKey(
        string $userId,
        string $resourceType,
        string $action,
    ): string {
        return sprintf('%s:auth:shares:%s:%s:%s:v%d', $this->tenantPrefix(), $userId, $resourceType, $action, $this->authVersion($userId));
    }

    private function tenantPrefix(): string
    {
        return $this->tenantContext->isResolved() ? $this->tenantContext->currentTenantSlug() : 'default';
    }

    private function authVersion(string $userId): int
    {
        /** @var int|string $version */
        $version = $this->cacheRepository->get(
            sprintf('%s:auth:version:%s', $this->tenantPrefix(), $userId),
            0,
        );

        return (int) $version;
    }
}
