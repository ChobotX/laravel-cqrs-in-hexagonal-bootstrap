<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Web\Settings\PreviewEmailTemplateRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;

it('validates with valid parameters', function (): void {
    $previewEmailTemplateRequest = PreviewEmailTemplateRequest::create('/test', 'POST', [
        'templateType' => 'user_invite',
        'locale' => 'cs',
        'subjectTemplate' => 'Welcome {{name}}',
        'bodyTemplate' => '<p>Hello {{name}}</p>',
    ]);
    $previewEmailTemplateRequest->setContainer(app());
    $previewEmailTemplateRequest->validateResolved();

    expect($previewEmailTemplateRequest->validated())->toBe([
        'templateType' => 'user_invite',
        'locale' => 'cs',
        'subjectTemplate' => 'Welcome {{name}}',
        'bodyTemplate' => '<p>Hello {{name}}</p>',
    ]);
});

it('rejects missing template type', function (): void {
    $previewEmailTemplateRequest = PreviewEmailTemplateRequest::create('/test', 'POST', [
        'locale' => 'cs',
        'subjectTemplate' => 'Subject',
        'bodyTemplate' => 'Body',
    ]);
    $previewEmailTemplateRequest->setContainer(app());
    $previewEmailTemplateRequest->setRedirector(app(Redirector::class));

    expect(fn () => $previewEmailTemplateRequest->validateResolved())->toThrow(ValidationException::class);
});

it('rejects missing locale', function (): void {
    $previewEmailTemplateRequest = PreviewEmailTemplateRequest::create('/test', 'POST', [
        'templateType' => 'user_invite',
        'subjectTemplate' => 'Subject',
        'bodyTemplate' => 'Body',
    ]);
    $previewEmailTemplateRequest->setContainer(app());
    $previewEmailTemplateRequest->setRedirector(app(Redirector::class));

    expect(fn () => $previewEmailTemplateRequest->validateResolved())->toThrow(ValidationException::class);
});

it('rejects subject template exceeding max length', function (): void {
    $previewEmailTemplateRequest = PreviewEmailTemplateRequest::create('/test', 'POST', [
        'templateType' => 'user_invite',
        'locale' => 'cs',
        'subjectTemplate' => str_repeat('x', 501),
        'bodyTemplate' => 'Body',
    ]);
    $previewEmailTemplateRequest->setContainer(app());
    $previewEmailTemplateRequest->setRedirector(app(Redirector::class));

    expect(fn () => $previewEmailTemplateRequest->validateResolved())->toThrow(ValidationException::class);
});

it('rejects missing body template', function (): void {
    $previewEmailTemplateRequest = PreviewEmailTemplateRequest::create('/test', 'POST', [
        'templateType' => 'user_invite',
        'locale' => 'cs',
        'subjectTemplate' => 'Subject',
    ]);
    $previewEmailTemplateRequest->setContainer(app());
    $previewEmailTemplateRequest->setRedirector(app(Redirector::class));

    expect(fn () => $previewEmailTemplateRequest->validateResolved())->toThrow(ValidationException::class);
});
