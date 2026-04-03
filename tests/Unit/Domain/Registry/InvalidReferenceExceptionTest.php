<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\InvalidReferenceException;

it('has correct status code', function (): void {
    $exception = new InvalidReferenceException('author', '550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with field and entry id', function (): void {
    $exception = new InvalidReferenceException('author', '550e8400-e29b-41d4-a716-446655440000');

    expect($exception->getMessage())->toBe('Field [author] references invalid entry [550e8400-e29b-41d4-a716-446655440000].');
});

it('translates user message', function (): void {
    $exception = new InvalidReferenceException('author', '550e8400-e29b-41d4-a716-446655440000');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.invalid_reference');
});

it('exposes field name and entry id', function (): void {
    $exception = new InvalidReferenceException('author', '550e8400-e29b-41d4-a716-446655440000');

    expect($exception->fieldName)->toBe('author')
        ->and($exception->entryId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
