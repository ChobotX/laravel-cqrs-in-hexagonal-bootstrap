<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Eloquent\Tenancy\TenantPreferenceModel;
use App\Infrastructure\Tenancy\TenantMigrator;
use App\Infrastructure\Tenancy\TenantSchemaManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

            /** @var array<string, mixed> $config */
            $config = $tenant->config;
            $displayName = is_string($config['display_name'] ?? null)
                ? $config['display_name']
                : Str::headline($tenant->slug);

            TenantPreferenceModel::writePreferences([
                'display_name' => $displayName,
                'logo_path' => null,
                'display_timezone' => null,
            ]);

            if (array_key_exists('display_name', $config)) {
                unset($config['display_name']);
                $tenant->config = $config;
                $tenant->save();
            }

            $this->call(TenantSeeder::class);

            $schemaManager->reset();
        }
    }
}
