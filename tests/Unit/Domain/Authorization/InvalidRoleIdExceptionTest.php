<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\InvalidRoleIdException;

it('has correct status code', function (): void {
    $exception = new InvalidRoleIdException('not-a-uuid');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with value', function (): void {
    $exception = new InvalidRoleIdException('not-a-uuid');

    expect($exception->getMessage())->toBe('Value [not-a-uuid] is not a valid role UUID.');
});

it('translates user message with value', function (): void {
    $exception = new InvalidRoleIdException('not-a-uuid');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key.':'.implode(',', $params);
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.invalid_role_id:not-a-uuid');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidRoleIdException('not-a-uuid');

    expect($exception->invalidValue)->toBe('not-a-uuid');
});
