<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Authorization\Contract\Service\AccessDecision;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;

final readonly class FakeAuthorizationChecker implements AuthorizationChecker
{
    /** @param list<string> $grantedPermissions */
    public function __construct(
        private array $grantedPermissions = [],
    ) {}

    public function can(string $userId, string $permission): bool
    {
        return in_array($permission, $this->grantedPermissions, true);
    }

    public function canWithScope(string $userId, string $permission): AccessDecision
    {
        $granted = $this->can($userId, $permission);

        return new readonly class($granted) implements AccessDecision
        {
            public function __construct(private bool $isGranted) {}

            public function granted(): bool
            {
                return $this->isGranted;
            }

            public function scope(): string
            {
                return 'all';
            }
        };
    }

    /** @return list<string> */
    public function accessibleResourceIds(string $userId, string $resourceType, string $action): array
    {
        return [];
    }
}
