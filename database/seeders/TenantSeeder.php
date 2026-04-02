<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\RolePermissionModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use App\Infrastructure\Eloquent\File\FileModel;
use App\Infrastructure\Eloquent\Label\LabelModel;
use App\Infrastructure\Eloquent\Team\TeamMemberModel;
use App\Infrastructure\Eloquent\Team\TeamModel;
use App\Infrastructure\Eloquent\User\UserModel;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class TenantSeeder extends Seeder
{
    private const string TEAM_MANAGERS_ID = '00000000-0000-0000-0000-000000000010';

    private const string TEAM_ENGINEERING_ID = '00000000-0000-0000-0000-000000000011';

    private const string TEAM_DESIGN_ID = '00000000-0000-0000-0000-000000000012';

    private const string TEAM_UX_ID = '00000000-0000-0000-0000-000000000013';

    private const string TEAM_BRAND_ID = '00000000-0000-0000-0000-000000000014';

    /** @var array<string, string> email => user_id */
    private array $userIds = [];

    public function run(): void
    {
        $this->seedTeams();

        $superAdminRole = $this->seedSuperAdminRole();
        $roles = $this->seedDefaultRoles();

        $managerRole = $roles['Manager'];
        $teamLeaderRole = $roles['Team Leader'];
        $teamMemberRole = $roles['Team Member'];
        $externistRole = $roles['Externist'];

        $admin = UserModel::factory()->create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin'),
        ]);
        $this->assignRole($admin->id, $superAdminRole->id);
        $this->userIds['admin@test.com'] = $admin->id;

        $this->seedManagersTeam($managerRole->id);
        $this->seedEngineeringTeam($teamLeaderRole->id, $teamMemberRole->id, $externistRole->id);
        $this->seedDesignTeam($teamLeaderRole->id, $teamMemberRole->id, $externistRole->id);
        $this->seedUxTeam($teamLeaderRole->id, $teamMemberRole->id, $externistRole->id);
        $this->seedBrandTeam($teamLeaderRole->id, $teamMemberRole->id, $externistRole->id);

        $this->seedCrossTeamMembers();
        $this->seedAvatars();
        $this->seedLabels();
        $this->call(NotificationSeeder::class);
    }

    private function seedTeams(): void
    {
        TeamModel::create([
            'id' => self::TEAM_MANAGERS_ID,
            'name' => 'Managers',
            'slug' => 'managers',
            'description' => 'Management team with visibility into all sub-teams',
        ]);

        TeamModel::create([
            'id' => self::TEAM_ENGINEERING_ID,
            'parent_team_id' => self::TEAM_MANAGERS_ID,
            'name' => 'Engineering',
            'slug' => 'engineering',
            'description' => 'Engineering sub-team',
        ]);

        TeamModel::create([
            'id' => self::TEAM_DESIGN_ID,
            'parent_team_id' => self::TEAM_MANAGERS_ID,
            'name' => 'Design',
            'slug' => 'design',
            'description' => 'Design sub-team',
        ]);

        TeamModel::create([
            'id' => self::TEAM_UX_ID,
            'parent_team_id' => self::TEAM_DESIGN_ID,
            'name' => 'UX Research',
            'slug' => 'ux-research',
            'description' => 'User experience research',
        ]);

        TeamModel::create([
            'id' => self::TEAM_BRAND_ID,
            'parent_team_id' => self::TEAM_DESIGN_ID,
            'name' => 'Brand & Identity',
            'slug' => 'brand-identity',
            'description' => 'Brand guidelines and visual identity',
        ]);
    }

    private function seedManagersTeam(string $managerRoleId): void
    {
        $this->createMember('Eva Collins', 'eva.collins@test.com', self::TEAM_MANAGERS_ID, $managerRoleId);
        $this->createMember('Frank Davis', 'frank.davis@test.com', self::TEAM_MANAGERS_ID, $managerRoleId);
        $this->createMember('Grace Miller', 'grace.miller@test.com', self::TEAM_MANAGERS_ID, $managerRoleId);
    }

    private function seedEngineeringTeam(string $teamLeaderRoleId, string $teamMemberRoleId, string $externistRoleId): void
    {
        $this->createMember('Henry Park', 'henry.park@test.com', self::TEAM_ENGINEERING_ID, $teamLeaderRoleId);
        $this->createMember('Irene Walsh', 'irene.walsh@test.com', self::TEAM_ENGINEERING_ID, $teamMemberRoleId);
        $this->createMember('Jack Turner', 'jack.turner@test.com', self::TEAM_ENGINEERING_ID, $teamMemberRoleId);
        $this->createMember('Karen Lopez', 'karen.lopez@test.com', self::TEAM_ENGINEERING_ID, $teamMemberRoleId);
        $this->createMember('Liam Chen', 'liam.chen@test.com', self::TEAM_ENGINEERING_ID, $teamMemberRoleId);
        $this->createMember('Mia Rivera', 'mia.rivera@test.com', self::TEAM_ENGINEERING_ID, $teamMemberRoleId);
        $this->createMember('Noah Kim', 'noah.kim@test.com', self::TEAM_ENGINEERING_ID, $teamMemberRoleId);
        $this->createMember('Olivia Scott', 'olivia.scott@test.com', self::TEAM_ENGINEERING_ID, $teamMemberRoleId);
        $this->createMember('Peter Yang', 'peter.yang@test.com', self::TEAM_ENGINEERING_ID, $teamMemberRoleId);
        $this->createMember('Raj Patel', 'raj.patel@test.com', self::TEAM_ENGINEERING_ID, $externistRoleId);
    }

    private function seedDesignTeam(string $teamLeaderRoleId, string $teamMemberRoleId, string $externistRoleId): void
    {
        $this->createMember('Sarah Blake', 'sarah.blake@test.com', self::TEAM_DESIGN_ID, $teamLeaderRoleId);
        $this->createMember('Tom Nguyen', 'tom.nguyen@test.com', self::TEAM_DESIGN_ID, $teamMemberRoleId);
        $this->createMember('Uma Frost', 'uma.frost@test.com', self::TEAM_DESIGN_ID, $teamMemberRoleId);
        $this->createMember('Victor Hall', 'victor.hall@test.com', self::TEAM_DESIGN_ID, $teamMemberRoleId);
        $this->createMember('Wendy Cruz', 'wendy.cruz@test.com', self::TEAM_DESIGN_ID, $externistRoleId);
    }

    private function seedUxTeam(string $teamLeaderRoleId, string $teamMemberRoleId, string $externistRoleId): void
    {
        $this->createMember('Xander Moore', 'xander.moore@test.com', self::TEAM_UX_ID, $teamLeaderRoleId);
        $this->createMember('Yuki Tanaka', 'yuki.tanaka@test.com', self::TEAM_UX_ID, $teamMemberRoleId);
        $this->createMember('Zoe Adams', 'zoe.adams@test.com', self::TEAM_UX_ID, $teamMemberRoleId);
        $this->createMember('Alex Reid', 'alex.reid@test.com', self::TEAM_UX_ID, $externistRoleId);
    }

    private function seedBrandTeam(string $teamLeaderRoleId, string $teamMemberRoleId, string $externistRoleId): void
    {
        $this->createMember('Beth Morgan', 'beth.morgan@test.com', self::TEAM_BRAND_ID, $teamLeaderRoleId);
        $this->createMember('Carlos Diaz', 'carlos.diaz@test.com', self::TEAM_BRAND_ID, $teamMemberRoleId);
        $this->createMember('Diana Webb', 'diana.webb@test.com', self::TEAM_BRAND_ID, $teamMemberRoleId);
        $this->createMember('Ethan Brooks', 'ethan.brooks@test.com', self::TEAM_BRAND_ID, $teamMemberRoleId);
        $this->createMember('Fiona Grant', 'fiona.grant@test.com', self::TEAM_BRAND_ID, $externistRoleId);
    }

    private function createMember(string $name, string $email, string $teamId, string $roleId): void
    {
        $user = UserModel::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('password'),
        ]);

        $this->assignRole($user->id, $roleId);
        $this->addTeamMember($user->id, $teamId);
        $this->userIds[$email] = $user->id;
    }

    private function addTeamMember(string $userId, string $teamId): void
    {
        TeamMemberModel::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'team_id' => $teamId,
            'joined_at' => now(),
        ]);
    }

    private function seedSuperAdminRole(): RoleModel
    {
        return RoleModel::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Super Admin',
            'description' => 'System super admin with all permissions',
            'is_system' => true,
        ]);
    }

    /**
     * @return array<string, RoleModel>
     */
    private function seedDefaultRoles(): array
    {
        /** @var array<string, array{features: array<string, array{actions: list<string>}>}> $modules */
        $modules = config('authorization.modules');

        $allPermissions = $this->allPermissionKeys($modules);
        $readPermissions = $this->filterByActions($modules, ['read']);
        $createUpdatePermissions = $this->filterByActions($modules, ['create', 'update']);
        $readCreateUpdatePermissions = $this->filterByActions($modules, ['read', 'create', 'update']);

        $roleDefinitions = [
            ['name' => 'Manager', 'description' => 'Full access within tenant', 'groups' => [
                ['permissions' => $allPermissions, 'scope' => 'all'],
            ]],
            ['name' => 'Team Leader', 'description' => 'Full access scoped to own team hierarchy', 'groups' => [
                ['permissions' => $allPermissions, 'scope' => 'team'],
            ]],
            ['name' => 'Team Member', 'description' => 'Can view all, create and update own resources', 'groups' => [
                ['permissions' => $readPermissions, 'scope' => 'all'],
                ['permissions' => $createUpdatePermissions, 'scope' => 'own'],
            ]],
            ['name' => 'Externist', 'description' => 'External collaborator with own resource access', 'groups' => [
                ['permissions' => $readCreateUpdatePermissions, 'scope' => 'own'],
            ]],
        ];

        $roles = [];

        foreach ($roleDefinitions as $definition) {
            $role = RoleModel::create([
                'id' => Str::uuid()->toString(),
                'name' => $definition['name'],
                'description' => $definition['description'],
                'is_system' => false,
            ]);

            foreach ($definition['groups'] as $group) {
                foreach ($group['permissions'] as [$module, $feature, $action]) {
                    RolePermissionModel::create([
                        'id' => Str::uuid()->toString(),
                        'role_id' => $role->id,
                        'module' => $module,
                        'feature' => $feature,
                        'action' => $action,
                        'scope' => $group['scope'],
                    ]);
                }
            }

            $roles[$definition['name']] = $role;
        }

        return $roles;
    }

    private function assignRole(string $userId, string $roleId): void
    {
        UserRoleModel::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'role_id' => $roleId,
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

    private function seedCrossTeamMembers(): void
    {
        // Liam Chen (Engineering) also in Design — cross-functional contributor
        $this->addTeamMember($this->userIds['liam.chen@test.com'], self::TEAM_DESIGN_ID);

        // Tom Nguyen (Design) also in Engineering — full-stack designer
        $this->addTeamMember($this->userIds['tom.nguyen@test.com'], self::TEAM_ENGINEERING_ID);

        // Yuki Tanaka (UX Research) also in Brand & Identity — same hierarchy level
        $this->addTeamMember($this->userIds['yuki.tanaka@test.com'], self::TEAM_BRAND_ID);
    }

    private function seedAvatars(): void
    {
        $usersWithAvatars = [
            'admin@test.com',
            'eva.collins@test.com',
            'frank.davis@test.com',
            'grace.miller@test.com',
            'henry.park@test.com',
            'irene.walsh@test.com',
            'karen.lopez@test.com',
            'liam.chen@test.com',
            'sarah.blake@test.com',
            'tom.nguyen@test.com',
            'xander.moore@test.com',
            'beth.morgan@test.com',
            'carlos.diaz@test.com',
        ];

        $filesystem = app(FilesystemFactory::class)->disk('files');
        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');

        foreach ($usersWithAvatars as $email) {
            $userId = $this->userIds[$email];
            $fileId = Str::uuid()->toString();
            $storagePath = sprintf('user-avatars/%s.png', $fileId);

            $filesystem->put($storagePath, $pixel);

            FileModel::create([
                'id' => $fileId,
                'namespace' => 'user-avatars',
                'original_name' => sprintf('%s-avatar.png', explode('@', $email)[0]),
                'storage_path' => $storagePath,
                'mime_type' => 'image/png',
                'size_in_bytes' => strlen($pixel),
                'version_number' => 1,
                'uploaded_by' => $userId,
                'uploaded_at' => now(),
            ]);

            UserModel::where('id', $userId)->update(['avatar_file_id' => $fileId]);
        }
    }

    private function seedLabels(): void
    {
        $userLabels = ['senior', 'junior', 'part_time', 'remote', 'on_site', 'contractor', 'mentor'];
        $teamLabels = ['product', 'platform', 'frontend', 'backend', 'cross_functional', 'client_facing'];

        $labelIds = [];

        foreach ($userLabels as $name) {
            $id = Str::uuid()->toString();
            LabelModel::create(['id' => $id, 'namespace' => 'users', 'name' => $name]);
            $labelIds["users:$name"] = $id;
        }

        foreach ($teamLabels as $name) {
            $id = Str::uuid()->toString();
            LabelModel::create(['id' => $id, 'namespace' => 'teams', 'name' => $name]);
            $labelIds["teams:$name"] = $id;
        }

        $userAssignments = [
            'admin@test.com' => ['senior', 'on_site', 'mentor'],
            'henry.park@test.com' => ['senior', 'on_site', 'mentor'],
            'irene.walsh@test.com' => ['senior', 'remote'],
            'jack.turner@test.com' => ['junior', 'on_site'],
            'karen.lopez@test.com' => ['senior', 'remote'],
            'liam.chen@test.com' => ['senior', 'on_site'],
            'mia.rivera@test.com' => ['junior', 'on_site'],
            'noah.kim@test.com' => ['junior', 'remote'],
            'olivia.scott@test.com' => ['senior', 'part_time'],
            'raj.patel@test.com' => ['contractor', 'remote'],
            'sarah.blake@test.com' => ['senior', 'on_site', 'mentor'],
            'tom.nguyen@test.com' => ['senior', 'on_site'],
            'uma.frost@test.com' => ['junior', 'remote'],
            'wendy.cruz@test.com' => ['contractor', 'remote'],
            'xander.moore@test.com' => ['senior', 'on_site'],
            'yuki.tanaka@test.com' => ['junior', 'on_site'],
            'beth.morgan@test.com' => ['senior', 'on_site'],
            'carlos.diaz@test.com' => ['junior', 'remote'],
            'ethan.brooks@test.com' => ['senior', 'part_time'],
            'fiona.grant@test.com' => ['contractor', 'remote'],
        ];

        foreach ($userAssignments as $email => $labels) {
            $userId = $this->userIds[$email];

            foreach ($labels as $label) {
                DB::table('label_assignments')->insert([
                    'label_id' => $labelIds["users:$label"],
                    'labelable_id' => $userId,
                ]);
            }
        }

        $teamAssignments = [
            self::TEAM_MANAGERS_ID => ['cross_functional'],
            self::TEAM_ENGINEERING_ID => ['platform', 'backend'],
            self::TEAM_DESIGN_ID => ['product', 'frontend', 'client_facing'],
            self::TEAM_UX_ID => ['product', 'client_facing'],
            self::TEAM_BRAND_ID => ['product', 'frontend'],
        ];

        foreach ($teamAssignments as $teamId => $labels) {
            foreach ($labels as $label) {
                DB::table('label_assignments')->insert([
                    'label_id' => $labelIds["teams:$label"],
                    'labelable_id' => $teamId,
                ]);
            }
        }
    }
}
