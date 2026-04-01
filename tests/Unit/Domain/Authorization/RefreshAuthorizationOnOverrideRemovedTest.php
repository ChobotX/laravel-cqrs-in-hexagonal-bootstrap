<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnOverrideRemoved;
use Tests\Helper\FakeAuthorizationRefresher;

it('refreshes authorization when override is removed', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnOverrideRemoved($refresher);
    $handler->handle(new PermissionOverrideRemoved('user-1', 'users.list.read', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->toBe(['user-1']);
});
