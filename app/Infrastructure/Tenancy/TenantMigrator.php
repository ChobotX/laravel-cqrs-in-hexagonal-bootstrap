<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Illuminate\Contracts\Console\Kernel as Artisan;

final readonly class TenantMigrator
{
    public function __construct(
        private TenantSchemaManager $schemaManager,
        private Artisan $artisan,
    ) {}

    public function setupTenant(TenantModel $tenant): void
    {
        $this->schemaManager->createSchema($tenant->schema_name);
        $this->schemaManager->switchTo($tenant);

        $this->artisan->call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        $this->schemaManager->reset();
    }

    public function migrateAll(): void
    {
        $tenants = TenantModel::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            $this->setupTenant($tenant);
        }
    }
}
