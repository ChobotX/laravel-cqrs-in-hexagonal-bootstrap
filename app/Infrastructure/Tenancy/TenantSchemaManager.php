<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenancy;

use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use Illuminate\Database\DatabaseManager;

final readonly class TenantSchemaManager
{
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {}

    public function switchTo(TenantModel $tenantModel): void
    {
        $needsPurge = $this->connectionDetailsChanged($tenantModel);

        config([
            'database.connections.tenant.host' => $tenantModel->database_host,
            'database.connections.tenant.port' => $tenantModel->database_port,
            'database.connections.tenant.database' => $tenantModel->database_name,
            'database.connections.tenant.username' => $tenantModel->database_username,
            'database.connections.tenant.password' => $tenantModel->database_password,
            'database.connections.tenant.search_path' => $tenantModel->schema_name,
        ]);

        if ($needsPurge) {
            $this->databaseManager->purge('tenant');
        }
    }

    public function createSchema(string $schemaName): void
    {
        $this->databaseManager->connection('landlord')
            ->statement(sprintf('CREATE SCHEMA IF NOT EXISTS "%s"', $schemaName));
    }

    public function dropSchema(string $schemaName): void
    {
        $this->databaseManager->connection('landlord')
            ->statement(sprintf('DROP SCHEMA IF EXISTS "%s" CASCADE', $schemaName));
    }

    public function reset(): void
    {
        config(['database.connections.tenant.search_path' => 'public']);
        $this->databaseManager->purge('tenant');
    }

    private function connectionDetailsChanged(TenantModel $tenantModel): bool
    {
        /** @var array{search_path: string, host: string, port: string|int, database: string, username: string} $cfg */
        $cfg = config('database.connections.tenant');

        return $cfg['search_path'] !== $tenantModel->schema_name
            || $cfg['host'] !== $tenantModel->database_host
            || (string) $cfg['port'] !== (string) $tenantModel->database_port
            || $cfg['database'] !== $tenantModel->database_name
            || $cfg['username'] !== $tenantModel->database_username;
    }
}
