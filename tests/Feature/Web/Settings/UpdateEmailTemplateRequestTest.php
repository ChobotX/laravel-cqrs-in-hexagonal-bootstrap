<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Web\Settings\UpdateEmailTemplateRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;

it('validates with valid parameters', function (): void {
    $updateEmailTemplateRequest = UpdateEmailTemplateRequest::create('/test', 'POST', [
        'subject_template' => 'Welcome {{name}}',
        'body_template' => '<p>Hello {{name}}</p>',
    ]);
    $updateEmailTemplateRequest->setContainer(app());
    $updateEmailTemplateRequest->validateResolved();

    expect($updateEmailTemplateRequest->validated())->toBe([
        'subject_template' => 'Welcome {{name}}',
        'body_template' => '<p>Hello {{name}}</p>',
    ]);
});

it('rejects missing subject template', function (): void {
    $updateEmailTemplateRequest = UpdateEmailTemplateRequest::create('/test', 'POST', [
        'body_template' => 'Body',
    ]);
    $updateEmailTemplateRequest->setContainer(app());
    $updateEmailTemplateRequest->setRedirector(app(Redirector::class));

    expect(fn () => $updateEmailTemplateRequest->validateResolved())->toThrow(ValidationException::class);
});

it('rejects missing body template', function (): void {
    $updateEmailTemplateRequest = UpdateEmailTemplateRequest::create('/test', 'POST', [
        'subject_template' => 'Subject',
    ]);
    $updateEmailTemplateRequest->setContainer(app());
    $updateEmailTemplateRequest->setRedirector(app(Redirector::class));

    expect(fn () => $updateEmailTemplateRequest->validateResolved())->toThrow(ValidationException::class);
});

it('rejects subject template exceeding max length', function (): void {
    $updateEmailTemplateRequest = UpdateEmailTemplateRequest::create('/test', 'POST', [
        'subject_template' => str_repeat('x', 501),
        'body_template' => 'Body',
    ]);
    $updateEmailTemplateRequest->setContainer(app());
    $updateEmailTemplateRequest->setRedirector(app(Redirector::class));

    expect(fn () => $updateEmailTemplateRequest->validateResolved())->toThrow(ValidationException::class);
});
