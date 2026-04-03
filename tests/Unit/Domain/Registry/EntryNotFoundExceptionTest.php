<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\EntryNotFoundException;

it('has correct status code', function (): void {
    $exception = new EntryNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(404);
});

it('has technical message with entry id', function (): void {
    $exception = new EntryNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->getMessage())->toBe('Entry [550e8400-e29b-41d4-a716-446655440000] not found.');
});

it('translates user message', function (): void {
    $exception = new EntryNotFoundException('550e8400-e29b-41d4-a716-446655440000');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.entry_not_found');
});

it('exposes entry id', function (): void {
    $exception = new EntryNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->entryId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
