<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\UserPermissionOverride;
use App\Domain\Authorization\Enum\OverrideType;
use App\Domain\Authorization\ValueObject\PermissionKey;

final class FakeUserPermissionRepository implements UserPermissionRepository
{
    /** @var array<string, list<Role>> */
    public array $userRolesMap = [];

    /** @var array<string, list<UserPermissionOverride>> */
    public array $userOverridesMap = [];

    /** @var list<array{userId: string, roleId: string}> */
    public array $assignedRoles = [];

    /** @var list<array{userId: string, roleId: string}> */
    public array $revokedRoles = [];

    /** @var array<string, list<string>> */
    public array $userIdsByRole = [];

    /** @return list<Role> */
    public function userRoles(string $userId): array
    {
        return $this->userRolesMap[$userId] ?? [];
    }

    /** @return array<string, list<Role>> */
    public function userRolesForUsers(array $userIds): array
    {
        $map = [];

        foreach ($userIds as $userId) {
            $map[$userId] = $this->userRoles($userId);
        }

        return $map;
    }

    public function assignRole(string $userId, RoleId $roleId): void
    {
        $this->assignedRoles[] = [
            'userId' => $userId,
            'roleId' => $roleId->value,
        ];
    }

    public function revokeRole(string $userId, RoleId $roleId): void
    {
        $this->revokedRoles[] = [
            'userId' => $userId,
            'roleId' => $roleId->value,
        ];
    }

    public function hasRole(string $userId, RoleId $roleId): bool
    {
        $roles = $this->userRoles($userId);

        return array_any($roles, fn ($role) => $role->id->equals($roleId));
    }

    /** @return list<UserPermissionOverride> */
    public function userOverrides(string $userId): array
    {
        return $this->userOverridesMap[$userId] ?? [];
    }

    public function setOverride(
        string $userId,
        PermissionKey $permissionKey,
        OverrideType $overrideType,
        AccessScope $accessScope,
    ): void {}

    public function removeOverride(string $userId, PermissionKey $permissionKey): void {}

    /** @return list<string> */
    public function userIdsWithRole(RoleId $roleId): array
    {
        return $this->userIdsByRole[$roleId->value] ?? [];
    }
}
