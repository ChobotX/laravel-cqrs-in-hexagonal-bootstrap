<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Repository;

use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\UserPermissionOverride;
use App\Domain\Authorization\Enum\OverrideType;
use App\Domain\Authorization\ValueObject\PermissionKey;

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
