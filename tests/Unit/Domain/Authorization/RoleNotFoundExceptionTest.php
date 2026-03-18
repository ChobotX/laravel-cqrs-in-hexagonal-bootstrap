<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Authorization\Exception\RoleNotFoundException;

it('has correct status code', function (): void {
    $exception = new RoleNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(404);
});

it('translates user message', function (): void {
    $exception = new RoleNotFoundException('550e8400-e29b-41d4-a716-446655440000');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key.':'.implode(',', $params);
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.role_not_found:550e8400-e29b-41d4-a716-446655440000');
});
