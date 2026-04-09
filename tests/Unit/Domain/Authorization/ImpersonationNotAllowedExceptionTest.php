<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\ImpersonationNotAllowedException;

it('has correct status code', function (): void {
    $exception = new ImpersonationNotAllowedException;

    expect($exception->statusCode())->toBe(403);
});

it('has technical message', function (): void {
    $exception = new ImpersonationNotAllowedException;

    expect($exception->getMessage())->toBe('Impersonation is not allowed.');
});

it('translates user message', function (): void {
    $exception = new ImpersonationNotAllowedException;
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
        ->toBe('messages.exceptions.impersonation_not_allowed');
});

it('accepts optional user id', function (): void {
    $exception = new ImpersonationNotAllowedException('user-1');

    expect($exception->userId)->toBe('user-1');
});
