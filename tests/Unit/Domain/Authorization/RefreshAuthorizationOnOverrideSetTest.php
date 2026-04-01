<?php

declare(strict_types=1);

use App\Domain\Authorization\Event\PermissionOverrideSet;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnOverrideSet;
use Tests\Helper\FakeAuthorizationRefresher;

it('refreshes authorization when override is set', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnOverrideSet($refresher);
    $handler->handle(new PermissionOverrideSet('user-1', 'users.list.read', 'grant', new DateTimeImmutable('2026-01-15T10:00:00+00:00')));

    expect($refresher->refreshedUserIds)->toBe(['user-1']);
});
