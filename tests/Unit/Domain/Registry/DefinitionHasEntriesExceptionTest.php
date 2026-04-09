<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\DefinitionHasEntriesException;

it('has correct status code', function (): void {
    $exception = new DefinitionHasEntriesException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(409);
});

it('has technical message with definition id', function (): void {
    $exception = new DefinitionHasEntriesException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->getMessage())->toBe('Definition [550e8400-e29b-41d4-a716-446655440000] has entries and cannot be deleted.');
});

it('translates user message', function (): void {
    $exception = new DefinitionHasEntriesException('550e8400-e29b-41d4-a716-446655440000');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.definition_has_entries');
});

it('exposes definition id', function (): void {
    $exception = new DefinitionHasEntriesException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
