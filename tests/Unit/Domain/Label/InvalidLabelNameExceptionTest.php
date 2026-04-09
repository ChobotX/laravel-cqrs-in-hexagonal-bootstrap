<?php

declare(strict_types=1);

use App\Domain\Label\Exception\InvalidLabelNameException;

it('has a technical message', function (): void {
    $exception = new InvalidLabelNameException('');

    expect($exception->getMessage())->toBe('Value [] is not a valid label name.')
        ->and($exception->invalidValue)->toBe('');
});

it('returns translated user message', function (): void {
    $exception = new InvalidLabelNameException('bad-name');

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

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_label_name [value=bad-name]');
});

it('returns 422 status code', function (): void {
    $exception = new InvalidLabelNameException('');

    expect($exception->statusCode())->toBe(422);
});
