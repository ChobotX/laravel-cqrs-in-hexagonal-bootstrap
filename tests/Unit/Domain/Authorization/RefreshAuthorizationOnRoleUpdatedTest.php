<?php

declare(strict_types=1);

use App\Domain\Authorization\Event\RoleUpdated;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleUpdated;
use Tests\Helper\FakeAuthorizationRefresher;
use Tests\Helper\FakeUserPermissionRepository;

it('refreshes authorization for all users with the updated role', function (): void {
    $refresher = new FakeAuthorizationRefresher;
    $userPermissionRepo = new FakeUserPermissionRepository;
    $userPermissionRepo->userIdsByRole = [
        '550e8400-e29b-41d4-a716-446655440000' => ['user-1', 'user-2', 'user-3'],
    ];

    $handler = new RefreshAuthorizationOnRoleUpdated($refresher, $userPermissionRepo);
    $handler->handle(new RoleUpdated('550e8400-e29b-41d4-a716-446655440000', 'Updated Role', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->toBe(['user-1', 'user-2', 'user-3']);
});

it('does nothing when no users have the updated role', function (): void {
    $refresher = new FakeAuthorizationRefresher;
    $userPermissionRepo = new FakeUserPermissionRepository;

    $handler = new RefreshAuthorizationOnRoleUpdated($refresher, $userPermissionRepo);
    $handler->handle(new RoleUpdated('550e8400-e29b-41d4-a716-446655440000', 'Updated Role', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->toBe([]);
});
