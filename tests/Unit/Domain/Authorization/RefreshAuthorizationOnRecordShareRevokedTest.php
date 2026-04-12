<?php

declare(strict_types=1);

use App\Domain\Authorization\Contract\Event\RecordShareRevoked;
use App\Domain\Authorization\EventHandler\RefreshAuthorizationOnRecordShareRevoked;
use Tests\Helper\FakeAuthorizationRefresher;

it('refreshes authorization for the grantee user when share is revoked', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnRecordShareRevoked($refresher);
    $handler->handle(new RecordShareRevoked(
        granteeUserId: 'user-1',
        resourceType: 'entry',
        resourceId: 'resource-1',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($refresher->refreshedUserIds)->toBe(['user-1']);
});

it('does not refresh other users when share is revoked', function (): void {
    $refresher = new FakeAuthorizationRefresher;

    $handler = new RefreshAuthorizationOnRecordShareRevoked($refresher);
    $handler->handle(new RecordShareRevoked(
        granteeUserId: 'user-1',
        resourceType: 'entry',
        resourceId: 'resource-1',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($refresher->refreshedUserIds)->not->toContain('user-2');
});
