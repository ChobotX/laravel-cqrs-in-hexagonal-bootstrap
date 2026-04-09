<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\Tenancy\Contract\Exception\TenantLogoStorageException;

it('creates exception with tenant id in message', function (): void {
    $exception = new TenantLogoStorageException('tenant-123');

    expect($exception->getMessage())->toContain('tenant-123');
});

it('returns internal server error status code', function (): void {
    $exception = new TenantLogoStorageException('tenant-123');

    expect($exception->statusCode())->toBe(HttpStatus::INTERNAL_SERVER_ERROR);
});

it('returns translated user message', function (): void {
    $exception = new TenantLogoStorageException('tenant-123');
    $translator = new Tests\Helper\FakeTranslator;

    $message = $exception->userMessage($translator);

    expect($message)->toBe('messages.exceptions.tenant_logo_storage_failed');
});
