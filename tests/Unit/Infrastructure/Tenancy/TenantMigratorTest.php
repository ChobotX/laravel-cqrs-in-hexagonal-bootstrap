<?php

declare(strict_types=1);

use App\Domain\Tenancy\Contract\Service\TenantProvisioner;
use App\Infrastructure\Tenancy\TenantMigrator;

uses(Tests\TestCase::class);

it('resets persistence scope on migrator', function (): void {
    config(['database.connections.tenant.search_path' => 'tenant_test,public']);

    app(TenantMigrator::class)->resetPersistenceScope();

    expect(config('database.connections.tenant.search_path'))->toBe('public');
});

it('resets tenant persistence scope on provisioner', function (): void {
    config(['database.connections.tenant.search_path' => 'tenant_test,public']);

    app(TenantProvisioner::class)->resetTenantPersistenceScope();

    expect(config('database.connections.tenant.search_path'))->toBe('public');
});
