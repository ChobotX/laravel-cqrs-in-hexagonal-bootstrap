<?php

declare(strict_types=1);

use App\Contract\Event\EventCollector;
use App\Domain\User\Contract\Event\UserCreated;
use App\Infrastructure\Tenancy\EloquentTenantAdminInitializer;
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
    $eventCollector = app(EventCollector::class);

    $eloquentTenantAdminInitializer = app(EloquentTenantAdminInitializer::class);
    $eloquentTenantAdminInitializer->initialize(
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

    // UserCreated event collected
    $events = $eventCollector->flush();
    $userCreatedEvents = array_filter(
        $events,
        fn (App\Contract\Event\DomainEvent $domainEvent): bool => $domainEvent instanceof UserCreated,
    );

    expect($userCreatedEvents)->toHaveCount(1);
    $event = array_values($userCreatedEvents)[0];
    expect($event->userId)->toBe('00000000-0000-0000-0000-000000000099')
        ->and($event->name)->toBe('Test Admin')
        ->and($event->email)->toBe('admin-init@test.com');
});
