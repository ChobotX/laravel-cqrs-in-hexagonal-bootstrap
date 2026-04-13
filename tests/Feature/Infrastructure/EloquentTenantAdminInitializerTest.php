<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Service\TenantAdminInitializer;
use App\Domain\User\Contract\Event\UserCreated;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    // Clear pre-seeded data to simulate a fresh tenant schema
    DB::connection('tenant')->table('user_roles')->delete();
    DB::connection('tenant')->table('users')->delete();
    DB::connection('tenant')->table('role_permissions')->delete();
    DB::connection('tenant')->table('roles')->delete();
    DB::connection('tenant')->table('email_templates')->delete();
});

it('initializes tenant with email templates, roles, admin user, and role assignment', function (): void {
    $tenantAdminInitializer = app(TenantAdminInitializer::class);
    $userCreated = $tenantAdminInitializer->initialize(
        '00000000-0000-0000-0000-000000000099',
        'Test Admin',
        'admin-init@test.com',
    );

    // Email templates seeded
    $this->assertDatabaseHas('email_templates', ['type' => 'user_invite', 'locale' => 'en'], 'tenant');
    $this->assertDatabaseHas('email_templates', ['type' => 'user_invite', 'locale' => 'cs'], 'tenant');

    // Default roles seeded
    $this->assertDatabaseHas('roles', ['name' => 'Manager'], 'tenant');
    $this->assertDatabaseHas('roles', ['name' => 'Team Leader'], 'tenant');
    $this->assertDatabaseHas('roles', ['name' => 'Team Member'], 'tenant');
    $this->assertDatabaseHas('roles', ['name' => 'Externist'], 'tenant');

    // Super Admin role created
    $this->assertDatabaseHas('roles', ['name' => 'Super Admin', 'is_system' => true], 'tenant');

    // Admin user created without password
    $this->assertDatabaseHas('users', [
        'id' => '00000000-0000-0000-0000-000000000099',
        'name' => 'Test Admin',
        'email' => 'admin-init@test.com',
        'password' => null,
    ], 'tenant');

    // Super Admin role assigned to admin
    $superAdminRoleId = DB::connection('tenant')
        ->table('roles')
        ->where('name', 'Super Admin')
        ->where('is_system', true)
        ->value('id');

    $this->assertDatabaseHas('user_roles', [
        'user_id' => '00000000-0000-0000-0000-000000000099',
        'role_id' => $superAdminRoleId,
    ], 'tenant');

    // UserCreated event returned to caller for collection
    expect($userCreated)->toBeInstanceOf(UserCreated::class)
        ->and($userCreated->userId)->toBe('00000000-0000-0000-0000-000000000099')
        ->and($userCreated->name)->toBe('Test Admin')
        ->and($userCreated->email)->toBe('admin-init@test.com');
});
