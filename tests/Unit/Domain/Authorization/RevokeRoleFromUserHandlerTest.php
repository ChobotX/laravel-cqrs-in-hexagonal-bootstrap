<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserCommand;
use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserHandler;
use App\Domain\Authorization\Contract\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Exception\RoleNotAssignedException;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeUserPermissionRepository;

it('revokes a role from a user and emits event', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['00000000-0000-0000-0000-000000000010'] = [
        new Role(new RoleId('550e8400-e29b-41d4-a716-446655440000'), new RoleName('Admin'), '', false, []),
    ];
    $eventCollector = new FakeEventCollector;

    $handler = new RevokeRoleFromUserHandler($userPermRepo, $eventCollector);

    $handler->handle(new RevokeRoleFromUserCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        roleId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($userPermRepo->revokedRoles)->toHaveCount(1);
    expect($userPermRepo->revokedRoles[0]['userId'])->toBe('00000000-0000-0000-0000-000000000010');
    expect($userPermRepo->revokedRoles[0]['roleId'])->toBe('550e8400-e29b-41d4-a716-446655440000');
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(RoleRevokedFromUser::class);

    $event = $eventCollector->collected[0];
    assert($event instanceof RoleRevokedFromUser);

    expect($event->userId)->toBe('00000000-0000-0000-0000-000000000010');
    expect($event->roleId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('throws when role is not assigned to user', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new RevokeRoleFromUserHandler($userPermRepo, $eventCollector);

    $handler->handle(new RevokeRoleFromUserCommand(
        userId: '00000000-0000-0000-0000-000000000010',
        roleId: '550e8400-e29b-41d4-a716-446655440000',
    ));
})->throws(RoleNotAssignedException::class);
