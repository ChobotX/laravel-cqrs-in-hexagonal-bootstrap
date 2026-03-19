<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\RolePermissionModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;
use App\Infrastructure\Eloquent\Organization\OrganizationModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class DatabaseSeeder extends Seeder
{
    private const string ORGANIZATION_ID = '00000000-0000-0000-0000-000000000001';

    public function run(): void
    {
        $this->seedOrganization();

        $superAdminRole = $this->seedSuperAdminRole();
        $roles = $this->seedDefaultRoles();

        $admin = UserModel::factory()->create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
        ]);

        $this->assignRole($admin->id, $superAdminRole->id);
        $this->addMember($admin->id);

        UserModel::factory()
            ->count(19)
            ->create()
            ->each(function (UserModel $user) use ($roles): void {
                $role = $roles[array_rand($roles)];
                $this->assignRole($user->id, $role->id);
                $this->addMember($user->id);
            });
    }

    private function seedOrganization(): void
    {
        OrganizationModel::create([
            'id' => self::ORGANIZATION_ID,
            'name' => 'Default Organization',
            'slug' => 'default',
            'description' => 'Default organization for development',
        ]);
    }

    private function addMember(string $userId): void
    {
        OrganizationMemberModel::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'organization_id' => self::ORGANIZATION_ID,
            'joined_at' => now(),
        ]);
    }

    private function seedSuperAdminRole(): RoleModel
    {
        return RoleModel::create([
            'id' => Str::uuid()->toString(),
            'organization_id' => null,
            'name' => 'Super Admin',
            'description' => 'System super admin with all permissions',
            'is_system' => true,
        ]);
    }

    /**
     * @return list<RoleModel>
     */
    private function seedDefaultRoles(): array
    {
        /** @var array<string, array{features: array<string, array{actions: list<string>}>}> $modules */
        $modules = config('authorization.modules');

        $allPermissions = $this->allPermissionKeys($modules);
        $readPermissions = $this->filterByActions($modules, ['read']);
        $readCreateUpdatePermissions = $this->filterByActions($modules, ['read', 'create', 'update']);

        $roleDefinitions = [
            ['name' => 'Admin', 'description' => 'Full access within organization', 'permissions' => $allPermissions, 'scope' => 'all'],
            ['name' => 'Editor', 'description' => 'Can read, create and update', 'permissions' => $readCreateUpdatePermissions, 'scope' => 'all'],
            ['name' => 'Viewer', 'description' => 'Read-only access', 'permissions' => $readPermissions, 'scope' => 'all'],
        ];

        $roles = [];

        foreach ($roleDefinitions as $definition) {
            $role = RoleModel::create([
                'id' => Str::uuid()->toString(),
                'organization_id' => self::ORGANIZATION_ID,
                'name' => $definition['name'],
                'description' => $definition['description'],
                'is_system' => false,
            ]);

            foreach ($definition['permissions'] as [$module, $feature, $action]) {
                RolePermissionModel::create([
                    'id' => Str::uuid()->toString(),
                    'role_id' => $role->id,
                    'module' => $module,
                    'feature' => $feature,
                    'action' => $action,
                    'scope' => $definition['scope'],
                ]);
            }

            $roles[] = $role;
        }

        return $roles;
    }

    private function assignRole(string $userId, string $roleId): void
    {
        UserRoleModel::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'role_id' => $roleId,
            'organization_id' => self::ORGANIZATION_ID,
        ]);
    }

    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $modules
     * @return list<array{0: string, 1: string, 2: string}>
     */
    private function allPermissionKeys(array $modules): array
    {
        $keys = [];

        foreach ($modules as $moduleName => $moduleConfig) {
            foreach ($moduleConfig['features'] as $featureName => $featureConfig) {
                foreach ($featureConfig['actions'] as $action) {
                    $keys[] = [$moduleName, $featureName, $action];
                }
            }
        }

        return $keys;
    }

    /**
     * @param  array<string, array{features: array<string, array{actions: list<string>}>}>  $modules
     * @param  list<string>  $actions
     * @return list<array{0: string, 1: string, 2: string}>
     */
    private function filterByActions(array $modules, array $actions): array
    {
        $keys = [];

        foreach ($modules as $moduleName => $moduleConfig) {
            foreach ($moduleConfig['features'] as $featureName => $featureConfig) {
                foreach ($featureConfig['actions'] as $action) {
                    if (in_array($action, $actions, true)) {
                        $keys[] = [$moduleName, $featureName, $action];
                    }
                }
            }
        }

        return $keys;
    }
}
