<?php

declare(strict_types=1);

use App\Infrastructure\Tenancy\TenantPreferencesSingletonMissingException;

it('includes tenant slug in message', function (): void {
    $exception = new TenantPreferencesSingletonMissingException('acme-demo');

    expect($exception->getMessage())->toContain('acme-demo')
        ->and($exception->getMessage())->toContain('tenant_preferences');
});
