<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Application\Bus\CommandBus;
use App\Contract\Event\EventCollector;
use App\Domain\Authorization\Contract\Command\SeedDefaultRolesCommand;
use App\Domain\EmailTemplate\Constant\DefaultEmailTemplates;
use App\Domain\Tenancy\Contract\Service\TenantAdminInitializer;
use App\Domain\User\Contract\Event\UserCreated;
use App\Infrastructure\Eloquent\Authorization\RoleModel;
use App\Infrastructure\Eloquent\Authorization\UserRoleModel;
use App\Infrastructure\Eloquent\User\UserModel;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class EloquentTenantAdminInitializer implements TenantAdminInitializer
{
    public function __construct(
        private CommandBus $commandBus,
        private EventCollector $eventCollector,
    ) {}

    public function initialize(string $adminId, string $adminName, string $adminEmail): void
    {
        $this->seedEmailTemplates();
        $this->commandBus->dispatch(new SeedDefaultRolesCommand);
        $roleModel = $this->createSuperAdminRole();
        $this->createAdminUser($adminId, $adminName, $adminEmail);
        $this->assignRole($adminId, $roleModel->id);
    }

    private function seedEmailTemplates(): void
    {
        foreach (DefaultEmailTemplates::DEFAULTS as $key => $template) {
            [$type, $locale] = explode(':', $key);

            DB::connection('tenant')->table('email_templates')->insert([
                'id' => Str::uuid()->toString(),
                'type' => $type,
                'locale' => $locale,
                'subject_template' => $template['subject'],
                'body_template' => $template['body'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createSuperAdminRole(): RoleModel
    {
        return RoleModel::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Super Admin',
            'description' => 'System super admin with all permissions',
            'is_system' => true,
        ]);
    }

    private function createAdminUser(string $adminId, string $adminName, string $adminEmail): void
    {
        UserModel::create([
            'id' => $adminId,
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => null,
        ]);

        $this->eventCollector->collect(new UserCreated(
            userId: $adminId,
            name: $adminName,
            email: $adminEmail,
            occurredAt: new DateTimeImmutable,
        ));
    }

    private function assignRole(string $userId, string $roleId): void
    {
        UserRoleModel::create([
            'id' => Str::uuid()->toString(),
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
    }
}
