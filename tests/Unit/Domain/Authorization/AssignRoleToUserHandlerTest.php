<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserCommand;
use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserHandler;
use App\Domain\Authorization\Contract\Event\RoleAssignedToUser;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Exception\DuplicateRoleAssignmentException;
use App\Domain\Authorization\Exception\RoleNotFoundException;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeRoleRepository;
use Tests\Helper\FakeUserPermissionRepository;

it('assigns a role to a user', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Editor role',
        false,
        [],
    );

    $roleRepo = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new AssignRoleToUserHandler($roleRepo, $userPermRepo, $eventCollector);

    $handler->handle(new AssignRoleToUserCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        roleId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($userPermRepo->assignedRoles)->toHaveCount(1);
    expect($userPermRepo->assignedRoles[0]['userId'])->toBe('00000000-0000-0000-0000-000000000010');
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(RoleAssignedToUser::class);
});

it('throws when role does not exist', function (): void {
    $roleRepo = new FakeRoleRepository;
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new AssignRoleToUserHandler($roleRepo, $userPermRepo, $eventCollector);

    $handler->handle(new AssignRoleToUserCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        roleId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(RoleNotFoundException::class);

it('throws on duplicate assignment', function (): void {
    $role = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440000'),
        new RoleName('Editor'),
        'Editor role',
        false,
        [],
    );

    $roleRepo = new FakeRoleRepository(['550e8400-e29b-41d4-a716-446655440000' => $role]);
    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['00000000-0000-0000-0000-000000000010'] = [$role];
    $eventCollector = new FakeEventCollector;

    $handler = new AssignRoleToUserHandler($roleRepo, $userPermRepo, $eventCollector);

    $handler->handle(new AssignRoleToUserCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        roleId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(DuplicateRoleAssignmentException::class);
