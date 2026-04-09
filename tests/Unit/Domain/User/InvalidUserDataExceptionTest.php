<?php

declare(strict_types=1);

use App\Domain\User\Contract\Exception\InvalidUserDataException;

it('has a technical message for empty name', function (): void {
    $invalidUserDataException = new InvalidUserDataException('User name must not be empty.');

    expect($invalidUserDataException->getMessage())->toBe('User name must not be empty.');
});

it('returns translated user message', function (): void {
    $invalidUserDataException = new InvalidUserDataException('User name must not be empty.');

    $translator = new class implements App\Contract\Translation\Translator
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

    expect($invalidUserDataException->userMessage($translator))->toBe('translated: messages.exceptions.invalid_user_data_empty_name');
});

it('returns 422 status code', function (): void {
    $invalidUserDataException = new InvalidUserDataException('User name must not be empty.');

    expect($invalidUserDataException->statusCode())->toBe(422);
});
