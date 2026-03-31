<?php

declare(strict_types=1);

use App\Domain\Notification\Command\MarkAllNotificationsAsRead\MarkAllNotificationsAsReadCommand;
use App\Domain\Notification\Command\MarkAllNotificationsAsRead\MarkAllNotificationsAsReadHandler;
use App\Domain\Notification\Event\AllNotificationsRead;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeNotificationRepository;

it('marks all notifications as read for the user', function (): void {
    $repo = new FakeNotificationRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new MarkAllNotificationsAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkAllNotificationsAsReadCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($repo->markedAllAsReadForRecipients)->toHaveCount(1)
        ->and($repo->markedAllAsReadForRecipients[0])->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('collects AllNotificationsRead event', function (): void {
    $repo = new FakeNotificationRepository;
    $eventCollector = new FakeEventCollector;

    $handler = new MarkAllNotificationsAsReadHandler($repo, $eventCollector);

    $handler->handle(new MarkAllNotificationsAsReadCommand(
        userId: '550e8400-e29b-41d4-a716-446655440000',
    ));

    expect($eventCollector->collected)->toHaveCount(1);
    expect($eventCollector->collected[0])->toBeInstanceOf(AllNotificationsRead::class);
    assert($eventCollector->collected[0] instanceof AllNotificationsRead);
    expect($eventCollector->collected[0]->recipientId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($eventCollector->collected[0]->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
});
