<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Handler\Command\InitializeTenantAdminHandler;
use App\Domain\User\Contract\Event\UserCreated;
use Tests\Helper\FakeEventCollector;
use Tests\Helper\FakeTenantAdminInitializer;
use Tests\Helper\FakeTenantBootstrapper;

it('bootstraps tenant, delegates to TenantAdminInitializer, and collects UserCreated', function (): void {
    $initializer = new FakeTenantAdminInitializer;
    $bootstrapper = new FakeTenantBootstrapper;
    $events = new FakeEventCollector;
    $handler = new InitializeTenantAdminHandler($initializer, $bootstrapper, $events);

    $handler->handle(new InitializeTenantAdminCommand(
        tenantSlug: 'test-tenant',
        adminId: 'admin-uuid',
        adminName: 'Jane Admin',
        adminEmail: 'jane@example.com',
    ));

    expect($bootstrapper->bootstrappedSlug)->toBe('test-tenant')
        ->and($initializer->initializedAdminId)->toBe('admin-uuid')
        ->and($initializer->initializedAdminName)->toBe('Jane Admin')
        ->and($initializer->initializedAdminEmail)->toBe('jane@example.com')
        ->and($events->collected)->toHaveCount(1)
        ->and($events->collected[0])->toBeInstanceOf(UserCreated::class);
});
