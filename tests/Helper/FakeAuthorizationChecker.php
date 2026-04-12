<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Authorization\Contract\Service\AccessDecision;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;

final readonly class FakeAuthorizationChecker implements AuthorizationChecker
{
    /**
     * @param  list<string>  $grantedPermissions
     * @param  array<string, string>  $permissionScopes  permission => scope (defaults to 'all')
     */
    public function __construct(
        private array $grantedPermissions = [],
        private array $permissionScopes = [],
    ) {}

    public function can(string $userId, string $permission): bool
    {
        return in_array($permission, $this->grantedPermissions, true);
    }

    public function canWithScope(string $userId, string $permission): AccessDecision
    {
        $granted = $this->can($userId, $permission);
        $scope = $this->permissionScopes[$permission] ?? 'all';

        return new readonly class($granted, $scope) implements AccessDecision
        {
            public function __construct(
                private bool $isGranted,
                private string $resolvedScope,
            ) {}

            public function granted(): bool
            {
                return $this->isGranted;
            }

            public function scope(): string
            {
                return $this->resolvedScope;
            }
        };
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
        return $this->can($userId, 'shareable.'.$resourceType.'.update');
    }

    public function canViewResourceShares(string $userId, string $resourceType): bool
    {
        return $this->can($userId, 'shareable.'.$resourceType.'.read');
    }
}
