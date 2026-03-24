<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\TenantMigrator;
use App\Infrastructure\Tenancy\TenantSchemaManager;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(LandlordSeeder::class);

        $migrator = app(TenantMigrator::class);
        $schemaManager = app(TenantSchemaManager::class);

        $tenants = TenantModel::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            $migrator->setupTenant($tenant);
            $schemaManager->switchTo($tenant);

            $this->call(TenantSeeder::class);

            $schemaManager->reset();
        }
    }
}
