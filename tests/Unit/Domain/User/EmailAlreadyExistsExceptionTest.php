<?php

declare(strict_types=1);

use App\Domain\User\Contract\Exception\EmailAlreadyExistsException;

it('has a technical message', function (): void {
    $emailAlreadyExistsException = new EmailAlreadyExistsException('john@example.com');

    expect($emailAlreadyExistsException->getMessage())->toBe('A user with email [john@example.com] already exists.')
        ->and($emailAlreadyExistsException->email)->toBe('john@example.com');
});

it('returns translated user message', function (): void {
    $emailAlreadyExistsException = new EmailAlreadyExistsException('john@example.com');

    $translator = new class implements App\Contract\Translation\Translator
    {
        /** @param array<string, string|int> $params */
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s [email=%s]', $key, $params['email']);
        }
    };

    expect($emailAlreadyExistsException->userMessage($translator))->toBe('translated: messages.exceptions.email_already_exists [email=john@example.com]');
});

it('returns 409 status code', function (): void {
    $emailAlreadyExistsException = new EmailAlreadyExistsException('john@example.com');

    expect($emailAlreadyExistsException->statusCode())->toBe(409);
});
