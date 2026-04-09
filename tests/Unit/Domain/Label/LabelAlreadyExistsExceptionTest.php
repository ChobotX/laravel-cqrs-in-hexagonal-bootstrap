<?php

declare(strict_types=1);

use App\Domain\Label\Contract\Exception\LabelAlreadyExistsException;

it('has a technical message', function (): void {
    $exception = new LabelAlreadyExistsException('users', 'important');

    expect($exception->getMessage())->toBe('A label [important] already exists in namespace [users].')
        ->and($exception->namespace)->toBe('users')
        ->and($exception->name)->toBe('important');
});

it('returns translated user message', function (): void {
    $exception = new LabelAlreadyExistsException('users', 'important');

    $translator = new class implements App\Contract\Translation\Translator
    {
        /** @param array<string, string|int> $params */
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s [name=%s]', $key, $params['name']);
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.label_already_exists [name=important]');
});

it('returns 409 status code', function (): void {
    $exception = new LabelAlreadyExistsException('users', 'important');

    expect($exception->statusCode())->toBe(409);
});
