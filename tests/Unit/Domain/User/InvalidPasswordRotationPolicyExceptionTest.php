<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\User\Exception\InvalidPasswordRotationPolicyException;
use Tests\Helper\FakeTranslator;

it('translates user message from key', function (): void {
    $exception = new InvalidPasswordRotationPolicyException('messages.exceptions.invalid_password_rotation_max_age');
    $translator = new FakeTranslator;

    expect($exception->userMessage($translator))->toBe('messages.exceptions.invalid_password_rotation_max_age');
});

it('uses unprocessable entity status', function (): void {
    expect(new InvalidPasswordRotationPolicyException('messages.exceptions.invalid_password_rotation_max_age')
        ->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY);
});
