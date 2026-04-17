<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Query\GetCurrentTenantNameQuery;
use App\Domain\Tenancy\Handler\Query\GetCurrentTenantNameHandler;
use Tests\Helper\FakeTenantContext;

it('returns tenant name when tenant is resolved', function (): void {
    $handler = new GetCurrentTenantNameHandler(new FakeTenantContext(tenantId: 'tenant-1', tenantName: 'Acme'));

    expect($handler->handle(new GetCurrentTenantNameQuery))->toBe('Acme');
});

it('returns null when tenant is not resolved', function (): void {
    $handler = new GetCurrentTenantNameHandler(new FakeTenantContext);

    expect($handler->handle(new GetCurrentTenantNameQuery))->toBeNull();
});
