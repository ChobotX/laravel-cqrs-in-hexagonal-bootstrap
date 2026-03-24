<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\ResolvedTenantContext;
use Illuminate\Support\Facades\DB;

trait TenantAwareRefreshDatabase
{
    protected static bool $tenantMigrationsRun = false;

    protected function setUpTenantAwareRefreshDatabase(): void
    {
        if (! static::$tenantMigrationsRun) {
            $this->runOnceSetup();
            static::$tenantMigrationsRun = true;
        }

        $this->switchToTenantSchema('tenant_test');
        $this->setTenantContext();
        $this->beginDatabaseTransactions();
    }

    protected function tearDownTenantAwareRefreshDatabase(): void
    {
        DB::connection('tenant')->rollBack();
        DB::connection('landlord')->rollBack();
    }

    private function runOnceSetup(): void
    {
        DB::connection('landlord')->statement('CREATE SCHEMA IF NOT EXISTS landlord');

        $this->artisan('migrate', [
            '--database' => 'landlord',
            '--path' => 'database/migrations/landlord',
            '--force' => true,
        ]);

        $this->seedTestTenants();

        DB::connection('landlord')->statement('CREATE SCHEMA IF NOT EXISTS "tenant_test"');
        $this->switchToTenantSchema('tenant_test');

        $this->artisan('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        DB::connection('landlord')->statement('CREATE SCHEMA IF NOT EXISTS "tenant_test_b"');
        $this->switchToTenantSchema('tenant_test_b');
        $this->artisan('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        $this->switchToTenantSchema('tenant_test');
    }

    private function seedTestTenants(): void
    {
        if (TenantModel::where('slug', 'test')->exists()) {
            return;
        }

        $password = (string) config('database.connections.tenant.password');

        TenantModel::create([
            'id' => '00000000-0000-0000-0000-000000000001',
            'name' => 'Test Tenant',
            'slug' => 'test',
            'schema_name' => 'tenant_test',
            'database_host' => (string) config('database.connections.tenant.host'),
            'database_port' => (int) config('database.connections.tenant.port'),
            'database_name' => (string) config('database.connections.tenant.database'),
            'database_username' => (string) config('database.connections.tenant.username'),
            'database_password' => $password,
            'is_active' => true,
            'config' => [],
        ]);

        TenantDomainModel::create([
            'tenant_id' => '00000000-0000-0000-0000-000000000001',
            'domain' => 'test',
            'is_primary' => true,
        ]);

        TenantModel::create([
            'id' => '00000000-0000-0000-0000-000000000002',
            'name' => 'Test Tenant B',
            'slug' => 'test-b',
            'schema_name' => 'tenant_test_b',
            'database_host' => (string) config('database.connections.tenant.host'),
            'database_port' => (int) config('database.connections.tenant.port'),
            'database_name' => (string) config('database.connections.tenant.database'),
            'database_username' => (string) config('database.connections.tenant.username'),
            'database_password' => $password,
            'is_active' => true,
            'config' => [],
        ]);

        TenantDomainModel::create([
            'tenant_id' => '00000000-0000-0000-0000-000000000002',
            'domain' => 'test-b',
            'is_primary' => true,
        ]);
    }

    private function switchToTenantSchema(string $schema): void
    {
        config(['database.connections.tenant.search_path' => $schema]);
        DB::purge('tenant');
    }

    private function setTenantContext(): void
    {
        $resolvedTenantContext = app(ResolvedTenantContext::class);
        $resolvedTenantContext->set('00000000-0000-0000-0000-000000000001', 'test');
    }

    private function beginDatabaseTransactions(): void
    {
        DB::connection('landlord')->beginTransaction();
        DB::connection('tenant')->beginTransaction();
    }
}
