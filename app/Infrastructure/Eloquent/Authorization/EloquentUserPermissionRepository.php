<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Action;
use App\Domain\Authorization\Feature;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\OverrideType;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleId;
use App\Domain\Authorization\UserPermissionOverride;
use App\Domain\Authorization\UserPermissionRepository;

final readonly class EloquentUserPermissionRepository implements UserPermissionRepository
{
    public function __construct(
        private RoleMapper $roleMapper,
    ) {}

    /** @return list<Role> */
    public function userRoles(string $userId): array
    {
        $roleIds = UserRoleModel::where('user_id', $userId)->pluck('role_id');

        $roles = RoleModel::with('permissions')
            ->whereIn('id', $roleIds)
            ->get();

        return array_values(
            $roles->map(fn (RoleModel $roleModel): Role => $this->roleMapper->toDomain($roleModel))->all(),
        );
    }

    public function assignRole(string $userId, RoleId $roleId): void
    {
        $userRoleModel = new UserRoleModel;
        $userRoleModel->user_id = $userId;
        $userRoleModel->role_id = $roleId->value;
        $userRoleModel->save();
    }

    public function revokeRole(string $userId, RoleId $roleId): void
    {
        UserRoleModel::where('user_id', $userId)
            ->where('role_id', $roleId->value)
            ->delete();
    }

    public function hasRole(string $userId, RoleId $roleId): bool
    {
        return UserRoleModel::where('user_id', $userId)
            ->where('role_id', $roleId->value)
            ->exists();
    }

    /** @return list<UserPermissionOverride> */
    public function userOverrides(string $userId): array
    {
        return array_values(
            UserPermissionOverrideModel::where('user_id', $userId)
                ->get()
                ->map(fn (UserPermissionOverrideModel $userPermissionOverrideModel): UserPermissionOverride => new UserPermissionOverride(
                    permissionKey: new PermissionKey(
                        module: new Module($userPermissionOverrideModel->module),
                        feature: $userPermissionOverrideModel->feature !== null ? new Feature($userPermissionOverrideModel->feature) : null,
                        action: $userPermissionOverrideModel->action !== null ? Action::from($userPermissionOverrideModel->action) : null,
                    ),
                    type: OverrideType::from($userPermissionOverrideModel->type),
                    scope: AccessScope::from($userPermissionOverrideModel->scope),
                ))
                ->all(),
        );
    }

    public function setOverride(
        string $userId,
        PermissionKey $permissionKey,
        OverrideType $overrideType,
        AccessScope $accessScope,
    ): void {
        UserPermissionOverrideModel::updateOrCreate(
            [
                'user_id' => $userId,
                'module' => $permissionKey->module->value,
                'feature' => $permissionKey->feature?->value,
                'action' => $permissionKey->action?->value,
            ],
            [
                'type' => $overrideType->value,
                'scope' => $accessScope->value,
            ],
        );
    }

    public function removeOverride(string $userId, PermissionKey $permissionKey): void
    {
        UserPermissionOverrideModel::where('user_id', $userId)
            ->where('module', $permissionKey->module->value)
            ->where('feature', $permissionKey->feature?->value)
            ->where('action', $permissionKey->action?->value)
            ->delete();
    }

    /** @return list<string> */
    public function userIdsWithRole(RoleId $roleId): array
    {
        /** @var list<string> $userIds */
        $userIds = array_values(
            UserRoleModel::where('role_id', $roleId->value)
                ->pluck('user_id')
                ->all(),
        );

        return $userIds;
    }
}
