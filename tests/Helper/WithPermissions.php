<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\RolePermissionModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use Illuminate\Support\Str;

trait WithPermissions
{
    protected function seedSuperAdminRole(): RoleModel
    {
        return RoleModel::create([
            'id' => '00000000-0000-0000-0000-000000000099',
            'organization_id' => null,
            'name' => 'Super Admin',
            'description' => 'System super admin',
            'is_system' => true,
        ]);
    }

    protected function assignSuperAdmin(string $userId): void
    {
        $role = RoleModel::where('is_system', true)->first();

        if (! $role instanceof RoleModel) {
            $role = $this->seedSuperAdminRole();
        }

        UserRoleModel::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'role_id' => $role->id,
            'organization_id' => '00000000-0000-0000-0000-000000000001',
        ]);
    }

    protected function seedRoleWithPermissions(
        string $organizationId,
        string $name,
        string $description,
        array $permissions,
    ): RoleModel {
        $roleModel = RoleModel::create([
            'id' => Str::uuid()->toString(),
            'organization_id' => $organizationId,
            'name' => $name,
            'description' => $description,
            'is_system' => false,
        ]);

        foreach ($permissions as $permKey => $scope) {
            $parts = explode('.', (string) $permKey);

            RolePermissionModel::create([
                'id' => Str::uuid()->toString(),
                'role_id' => $roleModel->id,
                'module' => $parts[0],
                'feature' => $parts[1] ?? null,
                'action' => $parts[2] ?? null,
                'scope' => $scope,
            ]);
        }

        return $roleModel;
    }

    protected function assignRole(string $userId, string $roleId, string $organizationId): void
    {
        UserRoleModel::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'role_id' => $roleId,
            'organization_id' => $organizationId,
        ]);
    }
}
