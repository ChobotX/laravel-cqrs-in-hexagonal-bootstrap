<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\EntryValidationException;

it('has correct status code', function (): void {
    $exception = new EntryValidationException(['field is required']);

    expect($exception->statusCode())->toBe(422);
});

it('has technical message', function (): void {
    $exception = new EntryValidationException(['field is required']);

    expect($exception->getMessage())->toBe('Entry data validation failed.');
});

it('translates user message', function (): void {
    $exception = new EntryValidationException(['field is required']);
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
        ->toBe('messages.exceptions.entry_validation_failed');
});

it('exposes validation errors', function (): void {
    $errors = ['name is required', 'email is invalid'];
    $exception = new EntryValidationException($errors);

    expect($exception->errors)->toBe($errors);
});
