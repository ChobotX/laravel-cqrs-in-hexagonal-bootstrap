<?php

declare(strict_types=1);

use App\Infrastructure\Notification\Exception\UnsupportedNotificationChannelException;

it('has message with channel name', function (): void {
    $exception = new UnsupportedNotificationChannelException('push');

    expect($exception->getMessage())->toBe('No notification channel sender registered for channel [push].');
});
