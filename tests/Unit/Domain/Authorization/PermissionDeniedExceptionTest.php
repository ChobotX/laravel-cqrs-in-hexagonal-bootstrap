<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Contract\Exception\PermissionDeniedException;

it('has correct status code', function (): void {
    $exception = new PermissionDeniedException;

    expect($exception->statusCode())->toBe(403);
});

it('includes permission in message when provided', function (): void {
    $exception = new PermissionDeniedException('users.list.create');

    expect($exception->getMessage())->toBe('Permission denied: users.list.create.');
});

it('has generic message without permission', function (): void {
    $exception = new PermissionDeniedException;

    expect($exception->getMessage())->toBe('Permission denied.');
});

it('translates user message', function (): void {
    $exception = new PermissionDeniedException;
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
        ->toBe('messages.exceptions.permission_denied');
});
