<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\Tenancy\Exception\TenantDisplayNameNotConfiguredException;
use Tests\Helper\FakeTranslator;

it('returns translated user message', function (): void {
    $exception = new TenantDisplayNameNotConfiguredException;

    expect($exception->userMessage(new FakeTranslator))
        ->toBe('messages.exceptions.tenant_display_name_not_configured');
});

it('uses internal server error status', function (): void {
    expect((new TenantDisplayNameNotConfiguredException)->statusCode())->toBe(HttpStatus::INTERNAL_SERVER_ERROR);
});
