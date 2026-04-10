<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\EmailTemplate\Exception\InvalidEmailTemplateIdException;
use App\Domain\EmailTemplate\Exception\InvalidEmailTemplateLocaleException;
use App\Domain\EmailTemplate\Exception\InvalidEmailTemplateTypeException;
use Tests\Helper\FakeTranslator;

it('InvalidEmailTemplateIdException has correct status code and user message', function (): void {
    $exception = new InvalidEmailTemplateIdException('bad-id');

    expect($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY)
        ->and($exception->userMessage(new FakeTranslator))->toBe('messages.exceptions.invalid_id')
        ->and($exception->invalidValue)->toBe('bad-id');
});

it('InvalidEmailTemplateLocaleException has correct status code and user message', function (): void {
    $exception = new InvalidEmailTemplateLocaleException('xyz');

    expect($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY)
        ->and($exception->userMessage(new FakeTranslator))->toBe('messages.exceptions.invalid_email_template_locale')
        ->and($exception->invalidValue)->toBe('xyz');
});

it('InvalidEmailTemplateTypeException has correct status code and user message', function (): void {
    $exception = new InvalidEmailTemplateTypeException('BAD TYPE');

    expect($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY)
        ->and($exception->userMessage(new FakeTranslator))->toBe('messages.exceptions.invalid_email_template_type')
        ->and($exception->invalidValue)->toBe('BAD TYPE');
});
