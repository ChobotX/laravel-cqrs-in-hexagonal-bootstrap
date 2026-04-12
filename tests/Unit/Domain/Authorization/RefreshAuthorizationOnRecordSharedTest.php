<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Event\RecordShared;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRecordShared;
use Tests\Helper\FakeAuthorizationRefresher;

it('refreshes authorization for the grantee user', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnRecordShared($refresher);
    $handler->handle(new RecordShared(
        granteeUserId: 'user-1',
        resourceType: 'entry',
        resourceId: 'resource-1',
        action: 'read',
        grantorUserId: 'grantor-1',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($refresher->refreshedUserIds)->toBe(['user-1']);
});

it('does not refresh other users', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnRecordShared($refresher);
    $handler->handle(new RecordShared(
        granteeUserId: 'user-1',
        resourceType: 'entry',
        resourceId: 'resource-1',
        action: 'read',
        grantorUserId: 'grantor-1',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($refresher->refreshedUserIds)->not->toContain('user-2');
});
