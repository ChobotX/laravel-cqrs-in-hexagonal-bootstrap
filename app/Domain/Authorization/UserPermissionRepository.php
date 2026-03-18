<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

interface UserPermissionRepository
{
    /** @return list<Role> */
    public function userRoles(string $userId, string $organizationId): array;

    public function assignRole(string $userId, RoleId $roleId, string $organizationId): void;

    public function revokeRole(string $userId, RoleId $roleId, string $organizationId): void;

    public function hasRole(string $userId, RoleId $roleId, string $organizationId): bool;

    /** @return list<UserPermissionOverride> */
    public function userOverrides(string $userId, string $organizationId): array;

    public function setOverride(
        string $userId,
        string $organizationId,
        PermissionKey $permissionKey,
        OverrideType $overrideType,
        AccessScope $accessScope,
    ): void;

    public function removeOverride(string $userId, string $organizationId, PermissionKey $permissionKey): void;

    /** @return list<string> */
    public function userIdsWithRole(RoleId $roleId): array;
}
