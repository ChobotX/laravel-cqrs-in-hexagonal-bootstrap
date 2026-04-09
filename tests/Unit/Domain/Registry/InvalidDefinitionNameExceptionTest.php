<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\InvalidDefinitionNameException;

it('has correct status code', function (): void {
    $exception = new InvalidDefinitionNameException('');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with invalid value', function (): void {
    $exception = new InvalidDefinitionNameException('');

    expect($exception->getMessage())->toBe('Value [] is not a valid definition name.');
});

it('translates user message', function (): void {
    $exception = new InvalidDefinitionNameException('');
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
        ->toBe('messages.exceptions.invalid_definition_name');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidDefinitionNameException('bad');

    expect($exception->invalidValue)->toBe('bad');
});
