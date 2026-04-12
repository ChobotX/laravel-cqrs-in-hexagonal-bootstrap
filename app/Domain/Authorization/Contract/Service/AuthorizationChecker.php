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

    /** Whether the resource type participates in record sharing. */
    public function supportsResourceSharing(string $resourceType): bool;

    /** Whether the actor may grant access to records of the given resource type. */
    public function canShareResource(string $userId, string $resourceType): bool;

    /** Whether the actor may inspect shares on records of the given resource type. */
    public function canViewResourceShares(string $userId, string $resourceType): bool;
}
