<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Contract\Tenancy\TenantProvisioner;
use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;

final readonly class EloquentTenantProvisioner implements TenantProvisioner
{
    public function __construct(
        private TenantMigrator $tenantMigrator,
        private TenantResolver $tenantResolver,
    ) {}

    public function createTenant(string $name, string $slug, ?string $domain): void
    {
        /** @var array{host: string, port: string|int, database: string, username: string, password: string} $cfg */
        $cfg = config('database.connections.tenant');

        /** @var string $schemaPrefix */
        $schemaPrefix = config('tenancy.schema_prefix');

        $tenant = TenantModel::create([
            'name' => $name,
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

        $this->tenantMigrator->setupTenant($tenant);
    }

    public function migrateTenant(string $slug): void
    {
        $tenantModel = $this->tenantResolver->resolveBySlug($slug);
        $this->tenantMigrator->setupTenant($tenantModel);
    }

    public function migrateAllTenants(): void
    {
        $this->tenantMigrator->migrateAll();
    }
}
