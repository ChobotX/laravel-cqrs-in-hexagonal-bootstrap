<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Crypt;

final readonly class TenantSchemaManager
{
    public function __construct(
        private DatabaseManager $db,
    ) {}

    public function switchTo(TenantModel $tenant): void
    {
        config([
            'database.connections.tenant.host' => $tenant->database_host,
            'database.connections.tenant.port' => $tenant->database_port,
            'database.connections.tenant.database' => $tenant->database_name,
            'database.connections.tenant.username' => $tenant->database_username,
            'database.connections.tenant.password' => $tenant->database_password,
            'database.connections.tenant.search_path' => $tenant->schema_name,
        ]);

        $this->db->purge('tenant');
    }

    public function createSchema(string $schemaName): void
    {
        $this->db->connection('landlord')
            ->statement(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $schemaName));
    }

    public function dropSchema(string $schemaName): void
    {
        $this->db->connection('landlord')
            ->statement(sprintf('DROP SCHEMA IF EXISTS "%s" CASCADE', $schemaName));
    }

    public function reset(): void
    {
        config(['database.connections.tenant.search_path' => 'public']);
        $this->db->purge('tenant');
    }
}
