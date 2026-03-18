<?php

declare(strict_types=1);

namespace App\Infrastructure\Authorization;

use App\Contract\Authorization\AccessDecision;
use App\Contract\Authorization\AuthorizationChecker;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

final readonly class CachedAuthorizationChecker implements AuthorizationChecker
{
    public function __construct(
        private AuthorizationChecker $authorizationChecker,
        private CacheRepository $cacheRepository,
        private int $ttl,
    ) {}

    public function can(string $userId, string $organizationId, string $permission): bool
    {
        /** @var array<string, bool> $permissions */
        $permissions = $this->cacheRepository->remember(
            $this->permissionsCacheKey($organizationId, $userId),
            $this->ttl,
            fn (): array => [],
        );

        if (isset($permissions[$permission])) {
            return $permissions[$permission];
        }

        $result = $this->authorizationChecker->can($userId, $organizationId, $permission);

        $permissions[$permission] = $result;
        $this->cacheRepository->put(
            $this->permissionsCacheKey($organizationId, $userId),
            $permissions,
            $this->ttl,
        );

        return $result;
    }

    public function canWithScope(string $userId, string $organizationId, string $permission): AccessDecision
    {
        return $this->authorizationChecker->canWithScope($userId, $organizationId, $permission);
    }

    /** @return list<string> */
    public function accessibleResourceIds(
        string $userId,
        string $organizationId,
        string $resourceType,
        string $action,
    ): array {
        $key = $this->sharesCacheKey($organizationId, $userId, $resourceType, $action);

        /* @var list<string> */
        return $this->cacheRepository->remember(
            $key,
            $this->ttl,
            fn (): array => $this->authorizationChecker->accessibleResourceIds($userId, $organizationId, $resourceType, $action),
        );
    }

    private function permissionsCacheKey(string $organizationId, string $userId): string
    {
        return sprintf('auth:perms:%s:%s', $organizationId, $userId);
    }

    private function sharesCacheKey(
        string $organizationId,
        string $userId,
        string $resourceType,
        string $action,
    ): string {
        return sprintf('auth:shares:%s:%s:%s:%s', $organizationId, $userId, $resourceType, $action);
    }
}
