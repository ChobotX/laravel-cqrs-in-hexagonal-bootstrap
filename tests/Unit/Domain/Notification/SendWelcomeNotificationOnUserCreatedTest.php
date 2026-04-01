<?php

declare(strict_types=1);

use App\Domain\Notification\Command\SendNotification\SendNotificationCommand;
use App\Domain\Notification\EventHandler\SendWelcomeNotificationOnUserCreated;
use App\Domain\Notification\NotificationLevel;
use App\Domain\Notification\WelcomeNotification;
use App\Domain\User\Event\UserCreated;
use Tests\Helper\FakeCommandBus;

it('dispatches SendNotificationCommand on user creation', function (): void {
    $commandBus = new FakeCommandBus;

    $handler = new SendWelcomeNotificationOnUserCreated($commandBus);

    $handler->handle(new UserCreated(
        userId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'John Doe',
        email: 'john@example.com',
        occurredAt: new DateTimeImmutable('2026-01-15T10:00:00+00:00'),
    ));

    expect($commandBus->dispatched)->toHaveCount(1);
    expect($commandBus->dispatched[0])->toBeInstanceOf(SendNotificationCommand::class);
    assert($commandBus->dispatched[0] instanceof SendNotificationCommand);
    expect($commandBus->dispatched[0]->recipientIds)->toBe(['550e8400-e29b-41d4-a716-446655440000'])
        ->and($commandBus->dispatched[0]->type)->toBe(WelcomeNotification::TYPE)
        ->and($commandBus->dispatched[0]->level)->toBe(NotificationLevel::Info->value)
        ->and($commandBus->dispatched[0]->link)->toBe(WelcomeNotification::LINK);
});
