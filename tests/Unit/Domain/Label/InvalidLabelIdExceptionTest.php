<?php

declare(strict_types=1);

use App\Domain\Label\Exception\InvalidLabelIdException;

it('has a technical message', function (): void {
    $exception = new InvalidLabelIdException('not-uuid');

    expect($exception->getMessage())->toBe('Value [not-uuid] is not a valid UUID.')
        ->and($exception->invalidValue)->toBe('not-uuid');
});

it('returns translated user message', function (): void {
    $exception = new InvalidLabelIdException('not-uuid');

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

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_label_id [value=not-uuid]');
});

it('returns 422 status code', function (): void {
    $exception = new InvalidLabelIdException('not-uuid');

    expect($exception->statusCode())->toBe(422);
});
