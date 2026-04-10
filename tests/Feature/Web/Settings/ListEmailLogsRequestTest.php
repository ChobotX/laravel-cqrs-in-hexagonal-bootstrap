<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Web\Settings\ListEmailLogsRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;

it('validates with valid parameters', function (): void {
    $listEmailLogsRequest = ListEmailLogsRequest::create('/test', 'GET', [
        'page' => 1,
        'per_page' => 25,
        'template_type' => 'user_invite',
        'recipient_id' => '550e8400-e29b-41d4-a716-446655440abc',
    ]);
    $listEmailLogsRequest->setContainer(app());
    $listEmailLogsRequest->validateResolved();

    expect($listEmailLogsRequest->templateType())->toBe('user_invite')
        ->and($listEmailLogsRequest->recipientId())->toBe('550e8400-e29b-41d4-a716-446655440abc');
});

it('validates without optional parameters', function (): void {
    $listEmailLogsRequest = ListEmailLogsRequest::create('/test', 'GET');
    $listEmailLogsRequest->setContainer(app());
    $listEmailLogsRequest->validateResolved();

    expect($listEmailLogsRequest->templateType())->toBeNull()
        ->and($listEmailLogsRequest->recipientId())->toBeNull();
});

it('returns null template type for empty string', function (): void {
    $listEmailLogsRequest = ListEmailLogsRequest::create('/test', 'GET', [
        'template_type' => '',
    ]);
    $listEmailLogsRequest->setContainer(app());
    $listEmailLogsRequest->validateResolved();

    expect($listEmailLogsRequest->templateType())->toBeNull();
});

it('returns null recipient id for empty string', function (): void {
    $listEmailLogsRequest = ListEmailLogsRequest::create('/test', 'GET', [
        'recipient_id' => '',
    ]);
    $listEmailLogsRequest->setContainer(app());
    $listEmailLogsRequest->setRedirector(app(Redirector::class));

    expect($listEmailLogsRequest->recipientId())->toBeNull();
});

it('rejects invalid recipient id format', function (): void {
    $listEmailLogsRequest = ListEmailLogsRequest::create('/test', 'GET', [
        'recipient_id' => 'not-a-uuid',
    ]);
    $listEmailLogsRequest->setContainer(app());
    $listEmailLogsRequest->setRedirector(app(Redirector::class));

    expect(fn () => $listEmailLogsRequest->validateResolved())->toThrow(ValidationException::class);
});

it('rejects page less than one', function (): void {
    $listEmailLogsRequest = ListEmailLogsRequest::create('/test', 'GET', [
        'page' => 0,
    ]);
    $listEmailLogsRequest->setContainer(app());
    $listEmailLogsRequest->setRedirector(app(Redirector::class));

    expect(fn () => $listEmailLogsRequest->validateResolved())->toThrow(ValidationException::class);
});

it('rejects per page exceeding maximum', function (): void {
    $listEmailLogsRequest = ListEmailLogsRequest::create('/test', 'GET', [
        'per_page' => 101,
    ]);
    $listEmailLogsRequest->setContainer(app());
    $listEmailLogsRequest->setRedirector(app(Redirector::class));

    expect(fn () => $listEmailLogsRequest->validateResolved())->toThrow(ValidationException::class);
});
