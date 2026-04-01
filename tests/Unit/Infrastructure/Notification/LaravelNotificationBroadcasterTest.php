<?php

declare(strict_types=1);

use App\Infrastructure\Notification\Broadcast\NewNotificationBroadcast;
use App\Infrastructure\Notification\Broadcast\UnreadCountUpdatedBroadcast;
use App\Infrastructure\Notification\LaravelNotificationBroadcaster;
use Tests\Helper\FakeEventsDispatcher;
use Tests\Helper\FakeTenantContext;

it('dispatches new notification broadcast event', function (): void {
    $dispatcher = new FakeEventsDispatcher;
    $tenantContext = new FakeTenantContext('tenant-1', 'acme');
    $broadcaster = new LaravelNotificationBroadcaster($dispatcher, $tenantContext);

    $broadcaster->broadcastNewNotification(
        recipientId: 'user-1',
        notificationId: 'notif-1',
        level: 'info',
        title: 'Test',
        body: 'Body',
        link: '/profile',
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    );

    expect($dispatcher->dispatched)->toHaveCount(1);

    $broadcast = $dispatcher->dispatched[0];
    assert($broadcast instanceof NewNotificationBroadcast);

    expect($broadcast->payload)->toBe([
        'id' => 'notif-1',
        'level' => 'info',
        'title' => 'Test',
        'body' => 'Body',
        'link_url' => '/profile',
        'read_at' => null,
        'created_at' => '2026-01-15T10:00:00+00:00',
    ]);
});

it('dispatches new notification broadcast with null link', function (): void {
    $dispatcher = new FakeEventsDispatcher;
    $tenantContext = new FakeTenantContext('tenant-1', 'acme');
    $broadcaster = new LaravelNotificationBroadcaster($dispatcher, $tenantContext);

    $broadcaster->broadcastNewNotification(
        recipientId: 'user-1',
        notificationId: 'notif-1',
        level: 'info',
        title: 'Test',
        body: 'Body',
        link: null,
        createdAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    );

    $broadcast = $dispatcher->dispatched[0];
    assert($broadcast instanceof NewNotificationBroadcast);

    expect($broadcast->payload['link_url'])->toBeNull();
});

it('dispatches unread count updated broadcast event', function (): void {
    $dispatcher = new FakeEventsDispatcher;
    $tenantContext = new FakeTenantContext('tenant-1', 'acme');
    $broadcaster = new LaravelNotificationBroadcaster($dispatcher, $tenantContext);

    $broadcaster->broadcastUnreadCountUpdated('user-1', 5);

    expect($dispatcher->dispatched)->toHaveCount(1);

    $broadcast = $dispatcher->dispatched[0];
    assert($broadcast instanceof UnreadCountUpdatedBroadcast);

    expect($broadcast->count)->toBe(5);
});
