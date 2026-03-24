<?php

declare(strict_types=1);

namespace App\Domain\Authorization;

interface UserPermissionRepository
{
    /** @return list<Role> */
    public function userRoles(string $userId): array;

    public function assignRole(string $userId, RoleId $roleId): void;

    public function revokeRole(string $userId, RoleId $roleId): void;

    public function hasRole(string $userId, RoleId $roleId): bool;

    /** @return list<UserPermissionOverride> */
    public function userOverrides(string $userId): array;

    public function setOverride(
        string $userId,
        PermissionKey $permissionKey,
        OverrideType $overrideType,
        AccessScope $accessScope,
    ): void;

    public function removeOverride(string $userId, PermissionKey $permissionKey): void;

    /** @return list<string> */
    public function userIdsWithRole(RoleId $roleId): array;
}
