<?php

declare(strict_types=1);

use App\Domain\Label\Exception\InvalidLabelNamespaceException;

it('has a technical message', function (): void {
    $exception = new InvalidLabelNamespaceException('BAD');

    expect($exception->getMessage())->toBe('Value [BAD] is not a valid label namespace.')
        ->and($exception->invalidValue)->toBe('BAD');
});

it('returns translated user message', function (): void {
    $exception = new InvalidLabelNamespaceException('BAD');

    $translator = new class implements App\Contract\Translation\Translator
    {
        /** @param array<string, string|int> $params */
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s [value=%s]', $key, $params['value']);
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_label_namespace [value=BAD]');
});

it('returns 422 status code', function (): void {
    $exception = new InvalidLabelNamespaceException('BAD');

    expect($exception->statusCode())->toBe(422);
});
