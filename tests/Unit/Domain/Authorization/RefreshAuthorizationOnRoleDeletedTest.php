<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Event\RoleDeleted;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleDeleted;
use Tests\Helper\FakeAuthorizationRefresher;
use Tests\Helper\FakeUserPermissionRepository;

it('refreshes authorization for all users with the deleted role', function (): void {
    $refresher = new FakeAuthorizationRefresher;
    $userPermissionRepo = new FakeUserPermissionRepository;
    $userPermissionRepo->userIdsByRole = [
        '550e8400-e29b-41d4-a716-446655440000' => ['user-1', 'user-2'],
    ];

    $handler = new RefreshAuthorizationOnRoleDeleted($refresher, $userPermissionRepo);
    $handler->handle(new RoleDeleted('550e8400-e29b-41d4-a716-446655440000', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->toBe(['user-1', 'user-2']);
});

it('does nothing when no users have the deleted role', function (): void {
    $refresher = new FakeAuthorizationRefresher;
    $userPermissionRepo = new FakeUserPermissionRepository;

    $handler = new RefreshAuthorizationOnRoleDeleted($refresher, $userPermissionRepo);
    $handler->handle(new RoleDeleted('550e8400-e29b-41d4-a716-446655440000', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->toBe([]);
});
