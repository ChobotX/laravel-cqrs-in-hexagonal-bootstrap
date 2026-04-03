<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\InvalidDefinitionVersionIdException;

it('has correct status code', function (): void {
    $exception = new InvalidDefinitionVersionIdException('bad-value');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with invalid value', function (): void {
    $exception = new InvalidDefinitionVersionIdException('bad-value');

    expect($exception->getMessage())->toBe('Value [bad-value] is not a valid UUID.');
});

it('translates user message', function (): void {
    $exception = new InvalidDefinitionVersionIdException('bad-value');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.invalid_definition_version_id');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidDefinitionVersionIdException('bad-value');

    expect($exception->invalidValue)->toBe('bad-value');
});
