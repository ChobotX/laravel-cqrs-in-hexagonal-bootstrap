<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\DefinitionAlreadyExistsException;

it('has correct status code', function (): void {
    $exception = new DefinitionAlreadyExistsException('crm', 'employees');

    expect($exception->statusCode())->toBe(409);
});

it('has technical message with namespace and slug', function (): void {
    $exception = new DefinitionAlreadyExistsException('crm', 'employees');

    expect($exception->getMessage())->toBe('A definition [employees] already exists in namespace [crm].');
});

it('translates user message', function (): void {
    $exception = new DefinitionAlreadyExistsException('crm', 'employees');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.definition_already_exists');
});

it('exposes namespace and slug', function (): void {
    $exception = new DefinitionAlreadyExistsException('crm', 'employees');

    expect($exception->namespace)->toBe('crm')
        ->and($exception->slug)->toBe('employees');
});
