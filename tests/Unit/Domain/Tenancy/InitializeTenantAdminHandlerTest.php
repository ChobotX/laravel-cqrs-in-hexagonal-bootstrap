<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Handler\Command\InitializeTenantAdminHandler;
use Tests\Helper\FakeTenantAdminInitializer;
use Tests\Helper\FakeTenantBootstrapper;

it('bootstraps tenant and delegates to TenantAdminInitializer', function (): void {
    $initializer = new FakeTenantAdminInitializer;
    $bootstrapper = new FakeTenantBootstrapper;
    $handler = new InitializeTenantAdminHandler($initializer, $bootstrapper);

    $handler->handle(new InitializeTenantAdminCommand(
        tenantSlug: 'test-tenant',
        adminId: 'admin-uuid',
        adminName: 'Jane Admin',
        adminEmail: 'jane@example.com',
    ));

    expect($bootstrapper->bootstrappedSlug)->toBe('test-tenant')
        ->and($initializer->initializedAdminId)->toBe('admin-uuid')
        ->and($initializer->initializedAdminName)->toBe('Jane Admin')
        ->and($initializer->initializedAdminEmail)->toBe('jane@example.com');
});
