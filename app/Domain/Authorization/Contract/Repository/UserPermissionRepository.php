<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Repository;

use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\UserPermissionOverride;
use App\Domain\Authorization\Enum\OverrideType;
use App\Domain\Authorization\ValueObject\PermissionKey;

/**
 * Persistence port for user permission data in the Authorization context; implementations live in Infrastructure.
 */
interface UserPermissionRepository
{
    /** @return list<Role> */
    public function userRoles(string $userId): array;

    /**
     * @param  list<string>  $userIds
     * @return array<string, list<Role>> userId => roles
     *                                   Contract operation `userRolesForUsers`; see infrastructure for behavior.
     */
    public function userRolesForUsers(array $userIds): array;

    /** Contract operation `assignRole`; see infrastructure for behavior. */
    public function assignRole(string $userId, RoleId $roleId): void;

    /** Contract operation `revokeRole`; see infrastructure for behavior. */
    public function revokeRole(string $userId, RoleId $roleId): void;

    /** Contract operation `hasRole`; see infrastructure for behavior. */
    public function hasRole(string $userId, RoleId $roleId): bool;

    /** @return list<UserPermissionOverride> */
    public function userOverrides(string $userId): array;

    /** Contract operation `setOverride`; see infrastructure for behavior. */
    public function setOverride(
        string $userId,
        PermissionKey $permissionKey,
        OverrideType $overrideType,
        AccessScope $accessScope,
    ): void;

    /** Deletes or soft-deletes the targeted record. */
    public function removeOverride(string $userId, PermissionKey $permissionKey): void;

    /** @return list<string> */
    public function userIdsWithRole(RoleId $roleId): array;
}
