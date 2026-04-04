<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Service;

interface AuthorizationChecker
{
    public function can(string $userId, string $permission): bool;

    public function canWithScope(string $userId, string $permission): AccessDecision;

    /** @return list<string> */
    public function accessibleResourceIds(
        string $userId,
        string $resourceType,
        string $action,
    ): array;
}
