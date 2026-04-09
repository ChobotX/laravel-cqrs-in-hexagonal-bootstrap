<?php

declare(strict_types=1);

use App\Domain\Label\Exception\LabelNotFoundException;

it('has a technical message', function (): void {
    $exception = new LabelNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->getMessage())->toBe('Label [550e8400-e29b-41d4-a716-446655440000] not found.')
        ->and($exception->labelId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('returns translated user message', function (): void {
    $exception = new LabelNotFoundException('550e8400-e29b-41d4-a716-446655440000');

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

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.label_not_found');
});

it('returns 404 status code', function (): void {
    $exception = new LabelNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(404);
});
