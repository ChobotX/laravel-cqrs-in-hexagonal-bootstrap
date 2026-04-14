<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\User\Contract\Exception\PasswordPreviouslyUsedException;
use Tests\Helper\FakeTranslator;

it('returns translated user message', function (): void {
    $exception = new PasswordPreviouslyUsedException;

    expect($exception->userMessage(new FakeTranslator))
        ->toBe('messages.exceptions.password_previously_used');
});

it('uses unprocessable entity status', function (): void {
    expect((new PasswordPreviouslyUsedException)->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY);
});

it('exposes a stable internal message', function (): void {
    expect((new PasswordPreviouslyUsedException)->getMessage())
        ->toBe('New password matches a recently used password.');
});
