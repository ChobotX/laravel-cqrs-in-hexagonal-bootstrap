<?php

declare(strict_types=1);

use App\Domain\Label\Exception\LabelNamespaceMismatchException;

it('has a technical message', function (): void {
    $exception = new LabelNamespaceMismatchException('550e8400-e29b-41d4-a716-446655440000', 'users', 'teams');

    expect($exception->getMessage())->toBe('Label [550e8400-e29b-41d4-a716-446655440000] belongs to namespace [teams], expected [users].')
        ->and($exception->labelId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($exception->expectedNamespace)->toBe('users')
        ->and($exception->actualNamespace)->toBe('teams');
});

it('returns translated user message', function (): void {
    $exception = new LabelNamespaceMismatchException('550e8400-e29b-41d4-a716-446655440000', 'users', 'teams');

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

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.label_namespace_mismatch');
});

it('returns 400 status code', function (): void {
    $exception = new LabelNamespaceMismatchException('550e8400-e29b-41d4-a716-446655440000', 'users', 'teams');

    expect($exception->statusCode())->toBe(400);
});
