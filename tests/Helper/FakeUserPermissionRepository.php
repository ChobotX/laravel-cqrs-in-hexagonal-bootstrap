<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Authorization\AccessScope;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\UserPermissionOverride;
use App\Domain\Authorization\UserPermissionRepository;

final class FakeUserPermissionRepository implements UserPermissionRepository
{
    /** @var array<string, list<Role>> */
    public array $userRolesMap = [];

    /** @var array<string, list<UserPermissionOverride>> */
    public array $userOverridesMap = [];

    /** @var list<array{userId: string, roleId: string, organizationId: string}> */
    public array $assignedRoles = [];

    /** @var list<array{userId: string, roleId: string, organizationId: string}> */
    public array $revokedRoles = [];

    /** @return list<Role> */
    public function userRoles(string $userId, string $organizationId): array
    {
        return $this->userRolesMap[$userId.':'.$organizationId] ?? [];
    }

    public function assignRole(string $userId, RoleId $roleId, string $organizationId): void
    {
        $this->assignedRoles[] = [
            'userId' => $userId,
            'roleId' => $roleId->value,
            'organizationId' => $organizationId,
        ];
    }

    public function revokeRole(string $userId, RoleId $roleId, string $organizationId): void
    {
        $this->revokedRoles[] = [
            'userId' => $userId,
            'roleId' => $roleId->value,
            'organizationId' => $organizationId,
        ];
    }

    public function hasRole(string $userId, RoleId $roleId, string $organizationId): bool
    {
        $roles = $this->userRoles($userId, $organizationId);

        return array_any($roles, fn ($role) => $role->id->equals($roleId));
    }

    /** @return list<UserPermissionOverride> */
    public function userOverrides(string $userId, string $organizationId): array
    {
        return $this->userOverridesMap[$userId.':'.$organizationId] ?? [];
    }

    public function setOverride(
        string $userId,
        string $organizationId,
        PermissionKey $permissionKey,
        OverrideType $overrideType,
        AccessScope $accessScope,
    ): void {}

    public function removeOverride(string $userId, string $organizationId, PermissionKey $permissionKey): void {}

    /** @return list<string> */
    public function userIdsWithRole(RoleId $roleId): array
    {
        return [];
    }
}
