<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Notification\Exception\NotificationOwnershipException;

it('exposes the notification id', function (): void {
    $exception = new NotificationOwnershipException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->notificationId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('has a technical message with the id', function (): void {
    $exception = new NotificationOwnershipException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->getMessage())->toBe('User does not own notification [550e8400-e29b-41d4-a716-446655440000].');
});

it('returns translated user message', function (): void {
    $exception = new NotificationOwnershipException('550e8400-e29b-41d4-a716-446655440000');

    $translator = new class implements Translator
    {
        /** @param array<string, string|int> $params */
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s', $key);
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.notification_ownership');
});

it('returns 403 status code', function (): void {
    $exception = new NotificationOwnershipException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(403);
});
