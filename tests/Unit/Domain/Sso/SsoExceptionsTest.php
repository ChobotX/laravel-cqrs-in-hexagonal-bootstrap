<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\Sso\Contract\Exception\SsoConfigurationConflictException;
use App\Domain\Sso\Contract\Exception\SsoConfigurationNotFoundException;
use App\Domain\Sso\Contract\Exception\SsoEnforcementViolationException;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Exception\InvalidSsoConfigurationIdException;
use App\Domain\Sso\Exception\InvalidSsoSlugException;
use App\Domain\Sso\Exception\SsoIdentityNotFoundException;
use Tests\Helper\FakeTranslator;

it('translates SsoConfigurationNotFoundException', function (): void {
    $exception = new SsoConfigurationNotFoundException('cfg-1');

    expect($exception->statusCode())->toBe(HttpStatus::NOT_FOUND)
        ->and($exception->userMessage(new FakeTranslator))->toContain('messages.exceptions.sso_configuration_not_found');
});

it('translates SsoConfigurationConflictException', function (): void {
    $exception = new SsoConfigurationConflictException('oidc', 'primary');

    expect($exception->statusCode())->toBe(HttpStatus::CONFLICT)
        ->and($exception->userMessage(new FakeTranslator))->toContain('messages.exceptions.sso_configuration_conflict');
});

it('translates SsoLoginRejectedException', function (): void {
    $exception = new SsoLoginRejectedException('reason');

    expect($exception->statusCode())->toBe(HttpStatus::FORBIDDEN)
        ->and($exception->userMessage(new FakeTranslator))->toContain('messages.exceptions.sso_login_rejected');
});

it('translates SsoEnforcementViolationException', function (): void {
    $exception = new SsoEnforcementViolationException;

    expect($exception->statusCode())->toBe(HttpStatus::FORBIDDEN)
        ->and($exception->userMessage(new FakeTranslator))->toContain('messages.exceptions.sso_enforcement_violation');
});

it('translates InvalidSsoConfigurationIdException', function (): void {
    $exception = new InvalidSsoConfigurationIdException('bad');

    expect($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY)
        ->and($exception->userMessage(new FakeTranslator))->toContain('messages.exceptions.invalid_sso_configuration_id');
});

it('translates InvalidSsoSlugException', function (): void {
    $exception = new InvalidSsoSlugException('BAD');

    expect($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY)
        ->and($exception->userMessage(new FakeTranslator))->toContain('messages.exceptions.invalid_sso_slug');
});

it('translates SsoIdentityNotFoundException', function (): void {
    $exception = new SsoIdentityNotFoundException('id-1');

    expect($exception->statusCode())->toBe(HttpStatus::NOT_FOUND)
        ->and($exception->userMessage(new FakeTranslator))->toContain('messages.exceptions.sso_identity_not_found');
});
