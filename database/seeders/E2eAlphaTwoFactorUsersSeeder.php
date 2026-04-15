<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use App\Infrastructure\Eloquent\Team\TeamMemberModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Eloquent\User\UserModel;
use App\Infrastructure\Tenancy\TenantSchemaManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Ensures Playwright two-factor specs have dedicated users on tenant `alpha`.
 * Requires tenant `alpha` to already contain roles (seed once via `tenant:setup` or equivalent).
 */
final class E2eAlphaTwoFactorUsersSeeder extends Seeder
{
    private const string TEAM_ENGINEERING_ID = '00000000-0000-0000-0000-000000000011';

    public function run(): void
    {
        $tenant = TenantModel::query()->where('slug', 'alpha')->first();
        if ($tenant === null) {
            throw new RuntimeException('Tenant alpha is missing; run tenant:setup.');
        }

        $schemaManager = app(TenantSchemaManager::class);
        $schemaManager->switchTo($tenant);

        try {
            // Keep e2e tenant templates in sync with current default template catalog.
            app(EmailTemplateSeeder::class)->run();

            $roleId = RoleModel::query()->where('name', 'Team Member')->value('id');
            if ($roleId === null) {
                throw new RuntimeException('Team Member role is missing on tenant alpha; run tenant:setup to seed the tenant.');
            }

            foreach (
                [
                    ['E2E 2FA TOTP', 'e2e-2fa-totp@test.com'],
                    ['E2E 2FA Email', 'e2e-2fa-email@test.com'],
                ] as [$name, $email]
            ) {
                $user = UserModel::firstOrCreate(
                    ['email' => $email],
                    ['name' => $name, 'password' => Hash::make('password')],
                );
                $user->name = $name;
                $user->password = Hash::make('password');
                $user->save();

                if (DB::connection('tenant')->table('user_roles')->where('user_id', $user->id)->where('role_id', $roleId)->doesntExist()) {
                    UserRoleModel::create([
                        'id' => (string) Str::uuid(),
                        'user_id' => $user->id,
                        'role_id' => $roleId,
                    ]);
                }

                if (DB::connection('tenant')->table('team_members')->where('user_id', $user->id)->where('team_id', self::TEAM_ENGINEERING_ID)->doesntExist()) {
                    TeamMemberModel::create([
                        'id' => (string) Str::uuid(),
                        'user_id' => $user->id,
                        'team_id' => self::TEAM_ENGINEERING_ID,
                        'joined_at' => now(),
                    ]);
                }
            }
        } finally {
            $schemaManager->reset();
        }
    }
}
