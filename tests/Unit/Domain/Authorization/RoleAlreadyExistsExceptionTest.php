<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\RoleAlreadyExistsException;

it('has correct status code', function (): void {
    $exception = new RoleAlreadyExistsException('Admin');

    expect($exception->statusCode())->toBe(409);
});

it('has technical message with name', function (): void {
    $exception = new RoleAlreadyExistsException('Admin');

    expect($exception->getMessage())->toBe('A role with name [Admin] already exists.');
});

it('translates user message with name', function (): void {
    $exception = new RoleAlreadyExistsException('Admin');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key.':'.implode(',', $params);
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.role_already_exists:Admin');
});

it('exposes role name', function (): void {
    $exception = new RoleAlreadyExistsException('Admin');

    expect($exception->name)->toBe('Admin');
});
