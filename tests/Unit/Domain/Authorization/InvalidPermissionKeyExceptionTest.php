<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\InvalidPermissionKeyException;

it('has correct status code', function (): void {
    $exception = new InvalidPermissionKeyException('bad.key');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with value', function (): void {
    $exception = new InvalidPermissionKeyException('bad.key');

    expect($exception->getMessage())->toBe('Value [bad.key] is not a valid permission key.');
});

it('translates user message with value', function (): void {
    $exception = new InvalidPermissionKeyException('bad.key');
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
        ->toBe('messages.exceptions.invalid_permission_key:bad.key');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidPermissionKeyException('bad.key');

    expect($exception->invalidValue)->toBe('bad.key');
});
