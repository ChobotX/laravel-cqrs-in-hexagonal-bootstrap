<?php

declare(strict_types=1);

use App\Infrastructure\Console\Tenancy\FreshCommand;
use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\TenantSchemaManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Tests\Helper\FakeArtisanKernel;

uses(Tests\TestCase::class);

it('drops registered tenant schemas, resets landlord, and calls tenant:setup', function (): void {
    $tenantSchemaManager = app(TenantSchemaManager::class);
    $databaseManager = app(DatabaseManager::class);
    $fakeArtisan = new FakeArtisanKernel;

    /** @var array{host: string, port: string|int, database: string, username: string, password: string} $cfg */
    $cfg = config('database.connections.tenant');

    // Clear existing test tenants so only our disposable one is dropped
    TenantModel::query()->delete();
    TenantDomainModel::query()->delete();

    // Register a disposable tenant with its own schema
    $tenantSchemaManager->createSchema('tenant_disposable');
    TenantModel::create([
        'name' => 'Disposable',
        'slug' => 'disposable',
        'schema_name' => 'tenant_disposable',
        'database_host' => $cfg['host'],
        'database_port' => (int) $cfg['port'],
        'database_name' => $cfg['database'],
        'database_username' => $cfg['username'],
        'database_password' => $cfg['password'],
        'is_active' => true,
        'config' => [],
    ]);

    $command = new FreshCommand;
    $command->setLaravel(app());
    $command->setInput(new Symfony\Component\Console\Input\ArrayInput([]));
    $command->setOutput(new Illuminate\Console\OutputStyle(
        new Symfony\Component\Console\Input\ArrayInput([]),
        new Symfony\Component\Console\Output\NullOutput,
    ));

    $exitCode = $command->handle($tenantSchemaManager, $databaseManager, $fakeArtisan);

    expect($exitCode)->toBe(0);

    // Schema should be gone
    $exists = DB::connection('landlord')
        ->selectOne("SELECT 1 FROM information_schema.schemata WHERE schema_name = 'tenant_disposable'");
    expect($exists)->toBeNull();

    // Landlord schema should exist (recreated)
    $landlordExists = DB::connection('landlord')
        ->selectOne("SELECT 1 FROM information_schema.schemata WHERE schema_name = 'landlord'");
    expect($landlordExists)->not->toBeNull();

    // tenant:setup should have been called
    expect($fakeArtisan->calls)->toBe(['tenant:setup']);

    // Restore landlord tables for subsequent tests
    $kernel = app(Illuminate\Contracts\Console\Kernel::class);
    $kernel->call('migrate', [
        '--database' => 'landlord',
        '--path' => 'database/migrations/landlord',
        '--force' => true,
    ]);
});
