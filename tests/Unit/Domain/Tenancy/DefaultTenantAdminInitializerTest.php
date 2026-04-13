<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Command\SeedDefaultRolesCommand;
use App\Domain\Tenancy\Service\DefaultTenantAdminInitializer;
use App\Domain\User\Contract\Event\UserCreated;
use App\Domain\User\Service\DefaultTenantAdminUserSnapshotFactory;
use Tests\Helper\FakeCommandBus;
use Tests\Helper\FakeIdGenerator;
use Tests\Helper\FakeRoleRepository;
use Tests\Helper\FakeTenantDefaultEmailTemplateSeeder;
use Tests\Helper\FakeUserPermissionRepository;
use Tests\Helper\FakeUserRepository;

it('seeds templates, dispatches seed roles, creates super admin and user, returns user created event, assigns role', function (): void {
    $seeder = new FakeTenantDefaultEmailTemplateSeeder;
    $commandBus = new FakeCommandBus;
    $roleRepo = new FakeRoleRepository;
    $userRepo = new FakeUserRepository;
    $userPermRepo = new FakeUserPermissionRepository;
    $idGenerator = new FakeIdGenerator;

    $initializer = new DefaultTenantAdminInitializer(
        $seeder,
        $commandBus,
        $roleRepo,
        $userRepo,
        $userPermRepo,
        $idGenerator,
        new DefaultTenantAdminUserSnapshotFactory,
    );

    $userCreated = $initializer->initialize(
        '00000000-0000-0000-0000-000000000099',
        'Admin Name',
        'admin@example.com',
    );

    expect($seeder->seedCallCount)->toBe(1)
        ->and($commandBus->dispatched)->toHaveCount(1)
        ->and($commandBus->dispatched[0])->toBeInstanceOf(SeedDefaultRolesCommand::class)
        ->and($roleRepo->saved)->toHaveCount(1)
        ->and($roleRepo->saved[0]->name->value)->toBe('Super Admin')
        ->and($userRepo->saved)->toHaveCount(1)
        ->and($userRepo->saved[0]->email->value)->toBe('admin@example.com')
        ->and($userCreated)->toBeInstanceOf(UserCreated::class)
        ->and($userCreated->userId)->toBe('00000000-0000-0000-0000-000000000099')
        ->and($userPermRepo->assignedRoles)->toHaveCount(1)
        ->and($userPermRepo->assignedRoles[0]['userId'])->toBe('00000000-0000-0000-0000-000000000099')
        ->and($userPermRepo->assignedRoles[0]['roleId'])->toBe($roleRepo->saved[0]->id->value);
});
