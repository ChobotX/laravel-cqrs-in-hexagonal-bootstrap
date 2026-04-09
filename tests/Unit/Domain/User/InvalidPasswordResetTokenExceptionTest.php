<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\User\Contract\Exception\InvalidPasswordResetTokenException;

it('has a technical message', function (): void {
    $exception = new InvalidPasswordResetTokenException;

    expect($exception->getMessage())->toBe('Invalid or expired password reset token.');
});

it('returns translated user message', function (): void {
    $exception = new InvalidPasswordResetTokenException;

    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s', $key);
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_password_reset_token');
});

it('returns 422 status code', function (): void {
    $exception = new InvalidPasswordResetTokenException;

    expect($exception->statusCode())->toBe(422);
});
