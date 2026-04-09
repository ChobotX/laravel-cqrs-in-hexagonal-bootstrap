<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Notification\Exception\InvalidNotificationIdException;

it('exposes the invalid value', function (): void {
    $exception = new InvalidNotificationIdException('bad-value');

    expect($exception->invalidValue)->toBe('bad-value');
});

it('has a technical message with the value', function (): void {
    $exception = new InvalidNotificationIdException('bad-value');

    expect($exception->getMessage())->toBe('Value [bad-value] is not a valid UUID.');
});

it('returns translated user message', function (): void {
    $exception = new InvalidNotificationIdException('bad-value');

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

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_notification_id [value=bad-value]');
});

it('returns 422 status code', function (): void {
    $exception = new InvalidNotificationIdException('bad-value');

    expect($exception->statusCode())->toBe(422);
});
