<?php

declare(strict_types=1);

use App\Domain\Authorization\Command\StartImpersonation\StartImpersonationCommand;
use App\Domain\Authorization\Command\StartImpersonation\StartImpersonationHandler;
use App\Domain\Authorization\Contract\Event\ImpersonationStarted;
use App\Domain\Authorization\Contract\RoleId;
use App\Domain\Authorization\Exception\ImpersonationNotAllowedException;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleName;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeImpersonationManager;
use Tests\Helper\FakeUserPermissionRepository;

it('starts impersonation for super admin', function (): void {
    $superAdmin = new Role(
        new RoleId('550e8400-e29b-41d4-a716-446655440001'),
        new RoleName('Super Admin'),
        'Super Admin',
        true,
        [],
    );

    $userPermRepo = new FakeUserPermissionRepository;
    $userPermRepo->userRolesMap['00000000-0000-0000-0000-000000000001'] = [$superAdmin];
    $impersonationManager = new FakeImpersonationManager;
    $eventCollector = new FakeEventCollector;

    $handler = new StartImpersonationHandler($userPermRepo, $impersonationManager, $eventCollector);

    $handler->handle(new StartImpersonationCommand(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
        targetUserId: '00000000-0000-0000-0000-000000000002',
    ));

    expect($impersonationManager->started)->toBeTrue();
    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(ImpersonationStarted::class);
});

it('throws when non-super-admin tries to impersonate', function (): void {
    $userPermRepo = new FakeUserPermissionRepository;
    $impersonationManager = new FakeImpersonationManager;
    $eventCollector = new FakeEventCollector;

    $handler = new StartImpersonationHandler($userPermRepo, $impersonationManager, $eventCollector);

    $handler->handle(new StartImpersonationCommand(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
        targetUserId: '00000000-0000-0000-0000-000000000002',
    ));
})->throws(ImpersonationNotAllowedException::class);
