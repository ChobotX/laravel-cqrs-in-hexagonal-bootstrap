<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Authorization;

use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Enum\AccessScope;
use App\Domain\Authorization\Contract\Enum\Action;
use App\Domain\Authorization\Contract\ValueObject\RoleId;
use App\Domain\Authorization\Contract\ValueObject\RolePermission;
use App\Domain\Authorization\ValueObject\Feature;
use App\Domain\Authorization\ValueObject\Module;
use App\Domain\Authorization\ValueObject\PermissionKey;
use App\Domain\Authorization\ValueObject\RoleName;

final readonly class RoleMapper
{
    public function toDomain(RoleModel $roleModel): Role
    {
        return new Role(
            id: new RoleId($roleModel->id),
            name: new RoleName($roleModel->name),
            description: $roleModel->description,
            isSystem: $roleModel->is_system,
            permissions: $roleModel->relationLoaded('permissions')
                ? array_values($roleModel->permissions->map(fn (RolePermissionModel $rolePermissionModel): RolePermission => $this->mapPermission($rolePermissionModel))->all())
                : [],
        );
    }

    private function mapPermission(RolePermissionModel $rolePermissionModel): RolePermission
    {
        return new RolePermission(
            permissionKey: new PermissionKey(
                module: new Module($rolePermissionModel->module),
                feature: $rolePermissionModel->feature !== null ? new Feature($rolePermissionModel->feature) : null,
                action: $rolePermissionModel->action !== null ? Action::from($rolePermissionModel->action) : null,
            ),
            scope: AccessScope::from($rolePermissionModel->scope),
        );
    }
}
