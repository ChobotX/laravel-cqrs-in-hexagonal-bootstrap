<?php

declare(strict_types=1);

use App\Domain\User\Exception\InvalidEmailException;

it('has a technical message', function (): void {
    $invalidEmailException = new InvalidEmailException('bad');

    expect($invalidEmailException->getMessage())->toBe('Value [bad] is not a valid email address.')
        ->and($invalidEmailException->invalidValue)->toBe('bad');
});

it('returns translated user message', function (): void {
    $invalidEmailException = new InvalidEmailException('bad');

    $translator = new class implements App\Contract\Translation\Translator
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

    expect($invalidEmailException->userMessage($translator))->toBe('translated: messages.exceptions.invalid_email [value=bad]');
});

it('returns 422 status code', function (): void {
    $invalidEmailException = new InvalidEmailException('bad');

    expect($invalidEmailException->statusCode())->toBe(422);
});
