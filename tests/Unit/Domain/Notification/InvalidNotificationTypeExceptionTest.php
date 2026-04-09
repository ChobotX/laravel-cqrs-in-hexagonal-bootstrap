<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Notification\Exception\InvalidNotificationTypeException;

it('exposes the invalid value', function (): void {
    $exception = new InvalidNotificationTypeException('BAD TYPE');

    expect($exception->invalidValue)->toBe('BAD TYPE');
});

it('has a technical message with the value', function (): void {
    $exception = new InvalidNotificationTypeException('BAD TYPE');

    expect($exception->getMessage())->toBe('Value [BAD TYPE] is not a valid notification type.');
});

it('returns translated user message', function (): void {
    $exception = new InvalidNotificationTypeException('BAD TYPE');

    $translator = new class implements Translator
    {
        /** @param array<string, string|int> $params */
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s [value=%s]', $key, $params['value']);
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_notification_type [value=BAD TYPE]');
});

it('returns 422 status code', function (): void {
    $exception = new InvalidNotificationTypeException('BAD TYPE');

    expect($exception->statusCode())->toBe(422);
});
