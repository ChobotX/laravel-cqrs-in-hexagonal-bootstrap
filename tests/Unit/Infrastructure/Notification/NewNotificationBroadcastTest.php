<?php

declare(strict_types=1);

use App\Infrastructure\Notification\Broadcast\NewNotificationBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

it('broadcasts on tenant-prefixed private channel', function (): void {
    $broadcast = new NewNotificationBroadcast(
        tenantSlug: 'acme',
        recipientId: '550e8400-e29b-41d4-a716-446655440000',
        payload: ['id' => 'abc', 'title' => 'Test'],
    );

    $channels = $broadcast->broadcastOn();

    expect($channels)->toHaveCount(1)
        ->and($channels[0])->toBeInstanceOf(PrivateChannel::class)
        ->and($channels[0]->name)->toBe('private-acme.notifications.550e8400-e29b-41d4-a716-446655440000');
});

it('uses NotificationReceived as event name', function (): void {
    $broadcast = new NewNotificationBroadcast(
        tenantSlug: 'acme',
        recipientId: '550e8400-e29b-41d4-a716-446655440000',
        payload: [],
    );

    expect($broadcast->broadcastAs())->toBe('NotificationReceived');
});

it('exposes payload', function (): void {
    $payload = ['id' => 'abc', 'title' => 'Test'];
    $broadcast = new NewNotificationBroadcast(
        tenantSlug: 'acme',
        recipientId: '550e8400-e29b-41d4-a716-446655440000',
        payload: $payload,
    );

    expect($broadcast->payload)->toBe($payload);
});
