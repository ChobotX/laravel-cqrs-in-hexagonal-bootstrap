<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\User\Contract\Exception\UserAlreadyActivatedException;

it('exposes the id', function (): void {
    $exception = new UserAlreadyActivatedException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('has a technical message with the id', function (): void {
    $exception = new UserAlreadyActivatedException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->getMessage())->toBe('User with id [550e8400-e29b-41d4-a716-446655440000] has already been activated.');
});

it('returns translated user message', function (): void {
    $exception = new UserAlreadyActivatedException('550e8400-e29b-41d4-a716-446655440000');

    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s', $key);
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.user_already_activated');
});

it('returns 422 status code', function (): void {
    $exception = new UserAlreadyActivatedException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(422);
});
