<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract;

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\UserPermissionOverride;

interface UserPermissionRepository
{
    /** @return list<Role> */
    public function userRoles(string $userId): array;

    /**
     * @param  list<string>  $userIds
     * @return array<string, list<Role>> userId => roles
     */
    public function userRolesForUsers(array $userIds): array;

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
