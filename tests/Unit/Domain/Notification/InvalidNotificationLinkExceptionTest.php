<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Notification\Exception\InvalidNotificationLinkException;

it('exposes the invalid value', function (): void {
    $exception = new InvalidNotificationLinkException('no-slash');

    expect($exception->invalidValue)->toBe('no-slash');
});

it('has a technical message with the value', function (): void {
    $exception = new InvalidNotificationLinkException('no-slash');

    expect($exception->getMessage())->toBe('Value [no-slash] is not a valid notification link.');
});

it('returns translated user message', function (): void {
    $exception = new InvalidNotificationLinkException('no-slash');

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

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_notification_link [value=no-slash]');
});

it('returns 422 status code', function (): void {
    $exception = new InvalidNotificationLinkException('no-slash');

    expect($exception->statusCode())->toBe(422);
});
