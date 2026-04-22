<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Domain\Tenancy\Contract\Service\TenantProvisioner;
use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;

final readonly class EloquentTenantProvisioner implements TenantProvisioner
{
    public function __construct(
        private TenantMigrator $tenantMigrator,
        private TenantResolver $tenantResolver,
        private TenantSchemaManager $tenantSchemaManager,
        private TenantDisplayPreferencesSync $tenantDisplayPreferencesSync,
    ) {}

    public function createTenant(string $slug, ?string $domain): void
    {
        /** @var array{host: string, port: string|int, database: string, username: string, password: string} $cfg */
        $cfg = config('database.connections.tenant');

        /** @var string $schemaPrefix */
        $schemaPrefix = config('tenancy.schema_prefix');

        $tenant = TenantModel::create([
            'slug' => $slug,
            'schema_name' => $schemaPrefix.$slug,
            'database_host' => $cfg['host'],
            'database_port' => (int) $cfg['port'],
            'database_name' => $cfg['database'],
            'database_username' => $cfg['username'],
            'database_password' => $cfg['password'],
            'is_active' => true,
            'config' => [],
        ]);

        if ($domain !== null) {
            TenantDomainModel::create([
                'tenant_id' => $tenant->id,
                'domain' => $domain,
                'is_primary' => true,
            ]);
        }

    }

    public function migrateTenant(string $slug, ?string $displayName = null): void
    {
        $tenantModel = $this->tenantResolver->resolveBySlug($slug);
        $this->tenantMigrator->setupTenant($tenantModel);
        $this->tenantSchemaManager->switchTo($tenantModel);

        try {
            $this->tenantDisplayPreferencesSync->sync($slug, $displayName);
        } finally {
            $this->tenantSchemaManager->reset();
        }
    }

    public function migrateAllTenants(): void
    {
        foreach (TenantModel::all() as $tenant) {
            $this->migrateTenant($tenant->slug);
        }
    }

    public function resetTenantPersistenceScope(): void
    {
        $this->tenantMigrator->resetPersistenceScope();
    }
}
