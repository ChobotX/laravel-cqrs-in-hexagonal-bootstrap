<?php

declare(strict_types=1);

use App\Infrastructure\Notification\Broadcast\UnreadCountUpdatedBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

it('broadcasts on private channel', function (): void {
    $broadcast = new UnreadCountUpdatedBroadcast(
        recipientId: '550e8400-e29b-41d4-a716-446655440000',
        count: 5,
    );

    $channels = $broadcast->broadcastOn();

    expect($channels)->toHaveCount(1)
        ->and($channels[0])->toBeInstanceOf(PrivateChannel::class)
        ->and($channels[0]->name)->toBe('private-notifications.550e8400-e29b-41d4-a716-446655440000');
});

it('uses UnreadCountUpdated as event name', function (): void {
    $broadcast = new UnreadCountUpdatedBroadcast(
        recipientId: '550e8400-e29b-41d4-a716-446655440000',
        count: 3,
    );

    expect($broadcast->broadcastAs())->toBe('UnreadCountUpdated');
});

it('exposes count', function (): void {
    $broadcast = new UnreadCountUpdatedBroadcast(
        recipientId: '550e8400-e29b-41d4-a716-446655440000',
        count: 7,
    );

    expect($broadcast->count)->toBe(7);
});
