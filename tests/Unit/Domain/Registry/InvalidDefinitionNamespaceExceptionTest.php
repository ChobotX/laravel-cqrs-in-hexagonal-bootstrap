<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\InvalidDefinitionNamespaceException;

it('has correct status code', function (): void {
    $exception = new InvalidDefinitionNamespaceException('Bad NS');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with invalid value', function (): void {
    $exception = new InvalidDefinitionNamespaceException('Bad NS');

    expect($exception->getMessage())->toBe('Value [Bad NS] is not a valid definition namespace.');
});

it('translates user message', function (): void {
    $exception = new InvalidDefinitionNamespaceException('Bad NS');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.invalid_definition_namespace');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidDefinitionNamespaceException('Bad NS');

    expect($exception->invalidValue)->toBe('Bad NS');
});
