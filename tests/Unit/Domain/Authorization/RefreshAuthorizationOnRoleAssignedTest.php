<?php

declare(strict_types=1);

use App\Domain\Authorization\Event\RoleAssignedToUser;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleAssigned;
use Tests\Helper\FakeAuthorizationRefresher;

it('refreshes authorization for the assigned user', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnRoleAssigned($refresher);
    $handler->handle(new RoleAssignedToUser('user-1', 'role-1', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->toBe(['user-1']);
});

it('does not refresh other users', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnRoleAssigned($refresher);
    $handler->handle(new RoleAssignedToUser('user-1', 'role-1', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->not->toContain('user-2');
});
