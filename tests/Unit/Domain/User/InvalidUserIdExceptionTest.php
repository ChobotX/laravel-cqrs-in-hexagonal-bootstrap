<?php

declare(strict_types=1);

use App\Domain\User\Exception\InvalidUserIdException;

it('has a technical message', function (): void {
    $invalidUserIdException = new InvalidUserIdException('not-uuid');

    expect($invalidUserIdException->getMessage())->toBe('Value [not-uuid] is not a valid UUID.')
        ->and($invalidUserIdException->invalidValue)->toBe('not-uuid');
});

it('returns translated user message', function (): void {
    $invalidUserIdException = new InvalidUserIdException('not-uuid');

    $translator = new class implements App\Contract\Translation\Translator
    {
        /** @param array<string, string|int> $params */
        public function translate(string $key, array $params = []): string
        {
            return sprintf('translated: %s [value=%s]', $key, $params['value']);
        }
    };

    expect($invalidUserIdException->userMessage($translator))->toBe('translated: messages.exceptions.invalid_user_id [value=not-uuid]');
});

it('returns 422 status code', function (): void {
    $invalidUserIdException = new InvalidUserIdException('not-uuid');

    expect($invalidUserIdException->statusCode())->toBe(422);
});
