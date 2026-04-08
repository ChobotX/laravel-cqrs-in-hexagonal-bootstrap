<?php

declare(strict_types=1);

use App\Presentation\Http\Request\Web\PaginationRequest;

it('returns raw filters from validated input', function (): void {
    $paginationRequest = PaginationRequest::create('/test', 'GET', [
        'filter' => ['status' => 'active', 'role' => 'admin'],
    ]);
    $paginationRequest->setContainer(app());
    $paginationRequest->validateResolved();

    expect($paginationRequest->rawFilters())->toBe(['status' => 'active', 'role' => 'admin']);
});

it('returns empty array when no filters provided', function (): void {
    $paginationRequest = PaginationRequest::create('/test', 'GET');
    $paginationRequest->setContainer(app());
    $paginationRequest->validateResolved();

    expect($paginationRequest->rawFilters())->toBe([]);
});

it('returns preset id when valid uuid provided', function (): void {
    $presetId = '550e8400-e29b-41d4-a716-446655440abc';

    $paginationRequest = PaginationRequest::create('/test', 'GET', [
        'preset' => $presetId,
    ]);
    $paginationRequest->setContainer(app());
    $paginationRequest->validateResolved();

    expect($paginationRequest->presetId())->toBe($presetId);
});

it('returns null when preset is empty', function (): void {
    $paginationRequest = PaginationRequest::create('/test', 'GET', [
        'preset' => '',
    ]);
    $paginationRequest->setContainer(app());
    $paginationRequest->validateResolved();

    expect($paginationRequest->presetId())->toBeNull();
});

it('returns null when preset is not provided', function (): void {
    $paginationRequest = PaginationRequest::create('/test', 'GET');
    $paginationRequest->setContainer(app());
    $paginationRequest->validateResolved();

    expect($paginationRequest->presetId())->toBeNull();
});

it('rejects invalid preset uuid format', function (): void {
    $paginationRequest = PaginationRequest::create('/test', 'GET', [
        'preset' => 'not-a-uuid',
    ]);
    $paginationRequest->setContainer(app());
    $paginationRequest->setRedirector(app(Illuminate\Routing\Redirector::class));

    expect(fn () => $paginationRequest->validateResolved())->toThrow(
        Illuminate\Validation\ValidationException::class,
    );
});

it('rejects filter values exceeding max length', function (): void {
    $paginationRequest = PaginationRequest::create('/test', 'GET', [
        'filter' => ['status' => str_repeat('x', 201)],
    ]);
    $paginationRequest->setContainer(app());
    $paginationRequest->setRedirector(app(Illuminate\Routing\Redirector::class));

    expect(fn () => $paginationRequest->validateResolved())->toThrow(
        Illuminate\Validation\ValidationException::class,
    );
});
