<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\EmailTemplate\Exception\EmailTemplateNotFoundException;
use Tests\Helper\FakeTranslator;

it('has correct status code and message', function (): void {
    $exception = new EmailTemplateNotFoundException('user_invite', 'en');
    $translator = new FakeTranslator;

    expect($exception->statusCode())->toBe(HttpStatus::NOT_FOUND)
        ->and($exception->type)->toBe('user_invite')
        ->and($exception->locale)->toBe('en')
        ->and($exception->getMessage())->toContain('user_invite')
        ->and($exception->getMessage())->toContain('en');

    $exception->userMessage($translator);

    expect($translator->calls)->toHaveCount(1)
        ->and($translator->calls[0]['key'])->toBe('messages.exceptions.email_template_not_found')
        ->and($translator->calls[0]['params'])->toBe(['type' => 'user_invite', 'locale' => 'en']);
});
