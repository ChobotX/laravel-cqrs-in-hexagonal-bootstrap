<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class LandlordSeeder extends Seeder
{
    public function run(): void
    {
        /** @var string $dbHost */
        $dbHost = config('database.connections.tenant.host');

        /** @var int $dbPort */
        $dbPort = (int) config('database.connections.tenant.port');

        /** @var string $dbName */
        $dbName = config('database.connections.tenant.database');

        /** @var string $dbUser */
        $dbUser = config('database.connections.tenant.username');

        /** @var string $dbPass */
        $dbPass = config('database.connections.tenant.password');

        $alpha = TenantModel::create([
            'id' => '00000000-0000-0000-0000-000000000100',
            'name' => 'Acme Corp',
            'slug' => 'alpha',
            'schema_name' => 'tenant_alpha',
            'database_host' => $dbHost,
            'database_port' => $dbPort,
            'database_name' => $dbName,
            'database_username' => $dbUser,
            'database_password' => $dbPass,
            'is_active' => true,
            'config' => [],
        ]);

        TenantDomainModel::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $alpha->id,
            'domain' => 'tenant-a',
            'is_primary' => true,
        ]);

        $bravo = TenantModel::create([
            'id' => '00000000-0000-0000-0000-000000000200',
            'name' => 'Globex Inc',
            'slug' => 'bravo',
            'schema_name' => 'tenant_bravo',
            'database_host' => $dbHost,
            'database_port' => $dbPort,
            'database_name' => $dbName,
            'database_username' => $dbUser,
            'database_password' => $dbPass,
            'is_active' => true,
            'config' => [],
        ]);

        TenantDomainModel::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $bravo->id,
            'domain' => 'tenant-b',
            'is_primary' => true,
        ]);
    }
}
