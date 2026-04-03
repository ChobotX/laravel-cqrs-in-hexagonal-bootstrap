<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\ReferencedDefinitionNotFoundException;

it('has correct status code', function (): void {
    $exception = new ReferencedDefinitionNotFoundException('crm', 'employees');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with namespace and slug', function (): void {
    $exception = new ReferencedDefinitionNotFoundException('crm', 'employees');

    expect($exception->getMessage())->toBe('Referenced definition [crm/employees] not found.');
});

it('translates user message', function (): void {
    $exception = new ReferencedDefinitionNotFoundException('crm', 'employees');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.referenced_definition_not_found');
});

it('exposes namespace and slug', function (): void {
    $exception = new ReferencedDefinitionNotFoundException('crm', 'employees');

    expect($exception->namespace)->toBe('crm')
        ->and($exception->slug)->toBe('employees');
});
