<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Event\RoleRevokedFromUser;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRoleRevoked;
use Tests\Helper\FakeAuthorizationRefresher;

it('refreshes authorization for the revoked user', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnRoleRevoked($refresher);
    $handler->handle(new RoleRevokedFromUser('user-1', 'role-1', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->toBe(['user-1']);
});
