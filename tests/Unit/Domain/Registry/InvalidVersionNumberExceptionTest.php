<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\InvalidVersionNumberException;

it('has correct status code', function (): void {
    $exception = new InvalidVersionNumberException(0);

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with invalid value', function (): void {
    $exception = new InvalidVersionNumberException(-5);

    expect($exception->getMessage())->toBe('Value [-5] is not a valid version number.');
});

it('translates user message', function (): void {
    $exception = new InvalidVersionNumberException(0);
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.invalid_version_number');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidVersionNumberException(-1);

    expect($exception->invalidValue)->toBe(-1);
});
