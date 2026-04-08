<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\ResolvedTenantContext;
use Illuminate\Support\Facades\Context;
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

        // Reset all scoped instances to ensure clean tenant context per test.
        // Without this, root-domain tests would see stale tenant context from previous tests.
        app()->forgetScopedInstances();

        $this->switchToTenantSchema($this->tenantSchema());
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
        $connection = DB::connection('landlord');

        // Serialize schema setup across parallel workers to prevent deadlocks
        $connection->statement('SELECT pg_advisory_lock(42)');

        try {
            $connection->statement('CREATE SCHEMA IF NOT EXISTS landlord');

            $this->artisan('migrate', [
                '--database' => 'landlord',
                '--path' => 'database/migrations/landlord',
                '--force' => true,
            ]);

            $this->seedTestTenants();

            $connection->statement(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $this->tenantSchema()));
            $this->switchToTenantSchema($this->tenantSchema());

            $this->artisan('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            $connection->statement(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $this->secondaryTenantSchema()));
            $this->switchToTenantSchema($this->secondaryTenantSchema());
            $this->artisan('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            $this->switchToTenantSchema($this->tenantSchema());
        } finally {
            $connection->statement('SELECT pg_advisory_unlock(42)');
        }
    }

    private function seedTestTenants(): void
    {
        $tenantId = $this->tenantId();
        $secondaryTenantId = $this->secondaryTenantId();
        $password = (string) config('database.connections.tenant.password');
        $token = $this->workerToken();

        TenantModel::updateOrCreate(['id' => $tenantId], [
            'name' => 'Test Tenant',
            'slug' => 'test-'.$token,
            'schema_name' => $this->tenantSchema(),
            'database_host' => (string) config('database.connections.tenant.host'),
            'database_port' => (int) config('database.connections.tenant.port'),
            'database_name' => (string) config('database.connections.tenant.database'),
            'database_username' => (string) config('database.connections.tenant.username'),
            'database_password' => $password,
            'is_active' => true,
            'config' => [],
        ]);

        TenantDomainModel::updateOrCreate(['tenant_id' => $tenantId], [
            'domain' => 'test-'.$token,
            'is_primary' => true,
        ]);

        TenantModel::updateOrCreate(['id' => $secondaryTenantId], [
            'name' => 'Test Tenant B',
            'slug' => 'test-b-'.$token,
            'schema_name' => $this->secondaryTenantSchema(),
            'database_host' => (string) config('database.connections.tenant.host'),
            'database_port' => (int) config('database.connections.tenant.port'),
            'database_name' => (string) config('database.connections.tenant.database'),
            'database_username' => (string) config('database.connections.tenant.username'),
            'database_password' => $password,
            'is_active' => true,
            'config' => [],
        ]);

        TenantDomainModel::updateOrCreate(['tenant_id' => $secondaryTenantId], [
            'domain' => 'test-b-'.$token,
            'is_primary' => true,
        ]);
    }

    private function switchToTenantSchema(string $schema): void
    {
        config(['database.connections.tenant.search_path' => $schema.',public']);
        DB::purge('tenant');
    }

    private function setTenantContext(): void
    {
        $resolvedTenantContext = app(ResolvedTenantContext::class);
        $resolvedTenantContext->set($this->tenantId(), 'test-'.$this->workerToken(), 'Test Tenant', null);

        Context::add('tenant_id', $this->tenantId());
        Context::add('tenant_slug', 'test-'.$this->workerToken());
    }

    private function beginDatabaseTransactions(): void
    {
        DB::connection('landlord')->beginTransaction();
        DB::connection('tenant')->beginTransaction();
    }

    /**
     * Per-worker token for parallel test isolation.
     * ParaTest sets TEST_TOKEN per worker; falls back to '1' for sequential runs.
     */
    private function workerToken(): string
    {
        $token = getenv('TEST_TOKEN');

        return $token !== false ? $token : '1';
    }

    private function tenantSchema(): string
    {
        return 'tenant_test_'.$this->workerToken();
    }

    private function secondaryTenantSchema(): string
    {
        return 'tenant_test_b_'.$this->workerToken();
    }

    private function tenantId(): string
    {
        $token = $this->workerToken();

        return sprintf('00000000-0000-0000-0000-%012d', (int) $token * 2 - 1);
    }

    private function secondaryTenantId(): string
    {
        $token = $this->workerToken();

        return sprintf('00000000-0000-0000-0000-%012d', (int) $token * 2);
    }
}
