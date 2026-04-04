<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\User\Contract\Exception\UserNotFoundException;

it('exposes the id', function (): void {
    $exception = new UserNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('has a technical message with the id', function (): void {
    $exception = new UserNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->getMessage())->toBe('User with id [550e8400-e29b-41d4-a716-446655440000] not found.');
});

it('returns translated user message', function (): void {
    $exception = new UserNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    $translator = new class implements Translator
    {
        /**
         * @param  array<string, string|int>  $params
         */
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s [id=%s]', $key, $params['id']);
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.user_not_found [id=550e8400-e29b-41d4-a716-446655440000]');
});

it('returns 404 status code', function (): void {
    $exception = new UserNotFoundException('550e8400-e29b-41d4-a716-446655440000');

    expect($exception->statusCode())->toBe(404);
});
