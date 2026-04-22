<?php

declare(strict_types=1);

namespace App\Contract\Auth;

/**
 * Cross-cutting permission + record-sharing gate used by bus middleware and orchestration handlers.
 */
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

    /** Whether the resource type participates in record sharing. */
    public function supportsResourceSharing(string $resourceType): bool;

    /** Whether the actor may grant access to records of the given resource type. */
    public function canShareResource(string $userId, string $resourceType): bool;

    /** Whether the actor may inspect shares on records of the given resource type. */
    public function canViewResourceShares(string $userId, string $resourceType): bool;
}
