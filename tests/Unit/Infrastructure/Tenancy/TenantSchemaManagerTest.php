<?php

declare(strict_types=1);

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\InvalidSchemaNameException;
use App\Infrastructure\Tenancy\TenantSchemaManager;

uses(Tests\TestCase::class);

/** @param  array<string, mixed>  $attributes */
function makeTenantModel(array $attributes = []): TenantModel
{
    /** @var array{host: string, port: string|int, database: string, username: string, password: string} $cfg */
    $cfg = config('database.connections.tenant');

    $model = new TenantModel;
    $model->forceFill(array_merge([
        'id' => '00000000-0000-0000-0000-000000000001',
        'name' => 'Test Tenant',
        'slug' => 'test',
        'schema_name' => 'tenant_test',
        'database_host' => $cfg['host'],
        'database_port' => (int) $cfg['port'],
        'database_name' => $cfg['database'],
        'database_username' => $cfg['username'],
        'database_password' => $cfg['password'],
        'is_active' => true,
        'config' => [],
    ], $attributes));

    return $model;
}

it('updates config on switchTo', function (): void {
    config(['database.connections.tenant.search_path' => 'public']);

    $tenantSchemaManager = app(TenantSchemaManager::class);
    $tenantSchemaManager->switchTo(makeTenantModel());

    expect(config('database.connections.tenant.search_path'))->toBe('tenant_test,public');
});

it('skips purge when connection details are unchanged', function (): void {
    $tenantSchemaManager = app(TenantSchemaManager::class);

    config(['database.connections.tenant.search_path' => 'tenant_test,public']);

    $pdo = Illuminate\Support\Facades\DB::connection('tenant')->getPdo();
    $tenantSchemaManager->switchTo(makeTenantModel());
    $connectionAfter = Illuminate\Support\Facades\DB::connection('tenant')->getPdo();

    expect($pdo)->toBe($connectionAfter);
});

it('purges when search_path changes', function (): void {
    config(['database.connections.tenant.search_path' => 'other_schema,public']);

    $tenantSchemaManager = app(TenantSchemaManager::class);
    $tenantSchemaManager->switchTo(makeTenantModel());

    expect(config('database.connections.tenant.search_path'))->toBe('tenant_test,public');
});

it('purges when host changes', function (): void {
    config(['database.connections.tenant.host' => 'old-host']);

    $tenantSchemaManager = app(TenantSchemaManager::class);
    $tenantModel = makeTenantModel();
    $tenantSchemaManager->switchTo($tenantModel);

    expect(config('database.connections.tenant.host'))->toBe($tenantModel->database_host);
});

it('creates and drops schema via landlord connection', function (): void {
    $tenantSchemaManager = app(TenantSchemaManager::class);
    $tenantSchemaManager->createSchema('tenant_drop_test');

    $exists = Illuminate\Support\Facades\DB::connection('landlord')
        ->selectOne("SELECT 1 FROM information_schema.schemata WHERE schema_name = 'tenant_drop_test'");
    expect($exists)->not->toBeNull();

    $tenantSchemaManager->dropSchema('tenant_drop_test');

    $exists = Illuminate\Support\Facades\DB::connection('landlord')
        ->selectOne("SELECT 1 FROM information_schema.schemata WHERE schema_name = 'tenant_drop_test'");
    expect($exists)->toBeNull();
});

it('rejects invalid schema name on create', function (): void {
    $tenantSchemaManager = app(TenantSchemaManager::class);
    $tenantSchemaManager->createSchema('invalid schema!');
})->throws(InvalidSchemaNameException::class);

it('rejects invalid schema name on drop', function (): void {
    $tenantSchemaManager = app(TenantSchemaManager::class);
    $tenantSchemaManager->dropSchema('"; DROP TABLE users; --');
})->throws(InvalidSchemaNameException::class);

it('purges when password changes', function (): void {
    $tenantSchemaManager = app(TenantSchemaManager::class);
    $tenantModel = makeTenantModel();

    // Set a different password in config AFTER model creation
    config(['database.connections.tenant.password' => 'old-password']);

    $tenantSchemaManager->switchTo($tenantModel);

    expect(config('database.connections.tenant.password'))->not->toBe('old-password');
});

it('resets search_path to public and purges', function (): void {
    config(['database.connections.tenant.search_path' => 'tenant_test,public']);

    $tenantSchemaManager = app(TenantSchemaManager::class);
    $tenantSchemaManager->reset();

    expect(config('database.connections.tenant.search_path'))->toBe('public');
});
