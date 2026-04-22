<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Command\CreateTenantCommand;
use App\Domain\Tenancy\Contract\Command\InitializeTenantAdminCommand;
use App\Domain\Tenancy\Contract\Command\RegisterTenantWithAdminCommand;
use App\Domain\Tenancy\Handler\Command\RegisterTenantWithAdminHandler;
use Tests\Helper\FakeCommandBus;

it('dispatches CreateTenantCommand then InitializeTenantAdminCommand', function (): void {
    $bus = new FakeCommandBus;
    $handler = new RegisterTenantWithAdminHandler($bus);

    $handler->handle(new RegisterTenantWithAdminCommand(
        name: 'Acme',
        slug: 'acme',
        domain: 'acme',
        adminId: 'admin-1',
        adminName: 'Root',
        adminEmail: 'root@acme.example',
    ));

    expect($bus->dispatched)->toHaveCount(2);

    $create = $bus->dispatched[0];
    assert($create instanceof CreateTenantCommand);
    expect($create->name)->toBe('Acme')
        ->and($create->slug)->toBe('acme')
        ->and($create->domain)->toBe('acme');

    $initAdmin = $bus->dispatched[1];
    assert($initAdmin instanceof InitializeTenantAdminCommand);
    expect($initAdmin->tenantSlug)->toBe('acme')
        ->and($initAdmin->adminId)->toBe('admin-1')
        ->and($initAdmin->adminName)->toBe('Root')
        ->and($initAdmin->adminEmail)->toBe('root@acme.example');
});
