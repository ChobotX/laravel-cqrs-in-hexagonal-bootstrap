<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Command\SeedDefaultRolesCommand;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Handler\Command\InitializeTenantAdminHandler;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Service\DefaultTenantAdminUserSnapshotFactory;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeRoleRepository;
use Tests\Helper\FakeTenantBootstrapper;
use Tests\Helper\FakeTenantDefaultEmailTemplateSeeder;
use Tests\Helper\FakeUserPermissionRepository;
use Tests\Helper\FakeUserRepository;

it('bootstraps tenant and orchestrates tenant admin setup in handler', function (): void {
    $templateSeeder = new FakeTenantDefaultEmailTemplateSeeder;
    $commandBus = new FakeCommandBus;
    $roleRepository = new FakeRoleRepository;
    $userRepository = new FakeUserRepository;
    $userPermissionRepository = new FakeUserPermissionRepository;
    $idGenerator = new FakeIdGenerator;
    $bootstrapper = new FakeTenantBootstrapper;
    $events = new FakeEventCollector;
    $handler = new InitializeTenantAdminHandler(
        $templateSeeder,
        $commandBus,
        $roleRepository,
        $userRepository,
        $userPermissionRepository,
        $idGenerator,
        new DefaultTenantAdminUserSnapshotFactory,
        $bootstrapper,
        $events,
    );

    $handler->handle(new InitializeTenantAdminCommand(
        tenantSlug: 'test-tenant',
        adminId: '00000000-0000-0000-0000-000000000099',
        adminName: 'Jane Admin',
        adminEmail: 'jane@example.com',
    ));

    expect($bootstrapper->bootstrappedSlug)->toBe('test-tenant')
        ->and($templateSeeder->seedCallCount)->toBe(1)
        ->and($commandBus->dispatched)->toHaveCount(1)
        ->and($commandBus->dispatched[0])->toBeInstanceOf(SeedDefaultRolesCommand::class)
        ->and($roleRepository->saved)->toHaveCount(1)
        ->and($userRepository->saved)->toHaveCount(1)
        ->and($userPermissionRepository->assignedRoles)->toHaveCount(1)
        ->and($events->collected)->toHaveCount(1)
        ->and($events->collected[0])->toBeInstanceOf(UserCreated::class);
});
