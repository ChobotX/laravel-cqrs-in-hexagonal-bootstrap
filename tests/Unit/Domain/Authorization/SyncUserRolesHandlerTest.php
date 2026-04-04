<?php

declare(strict_types=1);

use App\Contract\Authorization\AccessScope;
use App\Domain\Authorization\Command\SyncUserRoles\SyncUserRolesCommand;
use App\Domain\Authorization\Command\SyncUserRoles\SyncUserRolesHandler;
use App\Domain\Authorization\Contract\Event\RoleAssignedToUser;
use App\Domain\Authorization\Contract\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Contract\RolePermission;
use App\Domain\Authorization\Module;
use App\Domain\Authorization\PermissionKey;
use App\Domain\Authorization\PermissionResolver;
use App\Domain\Authorization\RoleAssignmentPolicy;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRoleRepository;
use Tests\Helper\FakeUserPermissionRepository;

function createSyncRolesHandler(
    FakeUserPermissionRepository $fakeUserPermissionRepository,
    FakeRoleRepository $fakeRoleRepository,
    FakeEventCollector $fakeEventCollector,
): SyncUserRolesHandler {
    return new SyncUserRolesHandler(
        userPermissionRepository: $fakeUserPermissionRepository,
        roleRepository: $fakeRoleRepository,
        permissionResolver: new PermissionResolver,
        roleAssignmentPolicy: new RoleAssignmentPolicy,
        eventCollector: $fakeEventCollector,
        availableModules: [
            'users' => [
                'features' => [
                    'list' => ['actions' => ['read', 'create', 'update', 'delete']],
                    'roles' => ['actions' => ['read', 'update']],
                ],
            ],
        ],
    );
}

function createRole(string $id, string $name, bool $isSystem = false): Role
{
    return new Role(new RoleId($id), new RoleName($name), $name, $isSystem, [
        new RolePermission(
            new PermissionKey(new Module('users')),
            AccessScope::All,
        ),
    ]);
}

it('assigns new roles and revokes removed roles', function (): void {
    $roleA = createRole('00000000-0000-0000-0000-00000000000a', 'Role A');
    $roleB = createRole('00000000-0000-0000-0000-00000000000b', 'Role B');
    $roleC = createRole('00000000-0000-0000-0000-00000000000c', 'Role C');

    $superAdmin = new Role(
        new RoleId('00000000-0000-0000-0000-ffffffffffff'),
        new RoleName('super-admin'),
        'Super Admin',
        true,
        [],
    );

    $roleRepo = new FakeRoleRepository([
        '00000000-0000-0000-0000-00000000000a' => $roleA,
        '00000000-0000-0000-0000-00000000000b' => $roleB,
        '00000000-0000-0000-0000-00000000000c' => $roleC,
        '00000000-0000-0000-0000-ffffffffffff' => $superAdmin,
    ]);

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['acting-user'] = [$superAdmin];
    $userPermRepo->userRolesMap['target-user'] = [$roleA, $roleB];

    $eventCollector = new FakeEventCollector;
    $syncUserRolesHandler = createSyncRolesHandler($userPermRepo, $roleRepo, $eventCollector);

    $syncUserRolesHandler->handle(new SyncUserRolesCommand(
        targetUserId: 'target-user',
        submittedRoleIds: ['00000000-0000-0000-0000-00000000000b', '00000000-0000-0000-0000-00000000000c'],
        actingUserId: 'acting-user',
    ));

    expect($userPermRepo->assignedRoles)->toHaveCount(1)
        ->and($userPermRepo->assignedRoles[0]['roleId'])->toBe('00000000-0000-0000-0000-00000000000c')
        ->and($userPermRepo->revokedRoles)->toHaveCount(1)
        ->and($userPermRepo->revokedRoles[0]['roleId'])->toBe('00000000-0000-0000-0000-00000000000a')
        ->and($eventCollector->collected)->toHaveCount(2)
        ->and($eventCollector->collected[0])->toBeInstanceOf(RoleAssignedToUser::class)
        ->and($eventCollector->collected[1])->toBeInstanceOf(RoleRevokedFromUser::class);
});

it('skips non-assignable roles', function (): void {
    $roleA = createRole('00000000-0000-0000-0000-00000000000a', 'Role A');

    $roleRepo = new FakeRoleRepository(['00000000-0000-0000-0000-00000000000a' => $roleA]);

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['acting-user'] = [];
    $userPermRepo->userRolesMap['target-user'] = [];

    $eventCollector = new FakeEventCollector;
    $syncUserRolesHandler = createSyncRolesHandler($userPermRepo, $roleRepo, $eventCollector);

    $syncUserRolesHandler->handle(new SyncUserRolesCommand(
        targetUserId: 'target-user',
        submittedRoleIds: ['00000000-0000-0000-0000-00000000000a'],
        actingUserId: 'acting-user',
    ));

    expect($userPermRepo->assignedRoles)->toBeEmpty()
        ->and($eventCollector->collected)->toBeEmpty();
});

it('does nothing when submitted matches current', function (): void {
    $roleA = createRole('00000000-0000-0000-0000-00000000000a', 'Role A');

    $superAdmin = new Role(
        new RoleId('00000000-0000-0000-0000-ffffffffffff'),
        new RoleName('super-admin'),
        'Super Admin',
        true,
        [],
    );

    $roleRepo = new FakeRoleRepository([
        '00000000-0000-0000-0000-00000000000a' => $roleA,
        '00000000-0000-0000-0000-ffffffffffff' => $superAdmin,
    ]);

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['acting-user'] = [$superAdmin];
    $userPermRepo->userRolesMap['target-user'] = [$roleA];

    $eventCollector = new FakeEventCollector;
    $syncUserRolesHandler = createSyncRolesHandler($userPermRepo, $roleRepo, $eventCollector);

    $syncUserRolesHandler->handle(new SyncUserRolesCommand(
        targetUserId: 'target-user',
        submittedRoleIds: ['00000000-0000-0000-0000-00000000000a'],
        actingUserId: 'acting-user',
    ));

    expect($userPermRepo->assignedRoles)->toBeEmpty()
        ->and($userPermRepo->revokedRoles)->toBeEmpty()
        ->and($eventCollector->collected)->toBeEmpty();
});
