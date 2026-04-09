<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\InvalidDefinitionSlugException;

it('has correct status code', function (): void {
    $exception = new InvalidDefinitionSlugException('Bad Slug');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with invalid value', function (): void {
    $exception = new InvalidDefinitionSlugException('Bad Slug');

    expect($exception->getMessage())->toBe('Value [Bad Slug] is not a valid definition slug.');
});

it('translates user message', function (): void {
    $exception = new InvalidDefinitionSlugException('Bad Slug');
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
        ->toBe('messages.exceptions.invalid_definition_slug');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidDefinitionSlugException('Bad Slug');

    expect($exception->invalidValue)->toBe('Bad Slug');
});
