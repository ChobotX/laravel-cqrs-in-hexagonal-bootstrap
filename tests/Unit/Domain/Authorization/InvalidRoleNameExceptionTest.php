<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\InvalidRoleNameException;

it('has correct status code', function (): void {
    $exception = new InvalidRoleNameException('');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message', function (): void {
    $exception = new InvalidRoleNameException('');

    expect($exception->getMessage())->toBe('Role name must not be empty.');
});

it('translates user message', function (): void {
    $exception = new InvalidRoleNameException('');
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
        ->toBe('messages.exceptions.invalid_role_name');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidRoleNameException('bad');

    expect($exception->invalidValue)->toBe('bad');
});
