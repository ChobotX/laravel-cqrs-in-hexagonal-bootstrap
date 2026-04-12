<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Service;

/**
 * Domain service contract for authorization in the Authorization bounded context.
 */
interface AuthorizationChecker
{
    /** Contract operation `can`; see infrastructure for behavior. */
    public function can(string $userId, string $permission): bool;

    /** Contract operation `canWithScope`; see infrastructure for behavior. */
    public function canWithScope(string $userId, string $permission): AccessDecision;

    /** @return list<string> */
    public function accessibleResourceIds(
        string $userId,
        string $resourceType,
        string $action,
    ): array;
}
