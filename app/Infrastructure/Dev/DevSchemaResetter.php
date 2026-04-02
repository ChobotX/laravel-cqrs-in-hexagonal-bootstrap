<?php

declare(strict_types=1);

namespace App\Infrastructure\Dev;

use Illuminate\Database\DatabaseManager;

final readonly class DevSchemaResetter
{
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {}

    public function resetAll(): void
    {
        $connection = $this->databaseManager->connection('landlord');

        /** @var list<object{schema_name: string}> $tenantSchemas */
        $tenantSchemas = $connection->select(
            "SELECT schema_name FROM information_schema.schemata WHERE schema_name LIKE 'tenant_%'",
        );

        foreach ($tenantSchemas as $tenantSchema) {
            $connection->statement(sprintf('DROP SCHEMA IF EXISTS "%s" CASCADE', $tenantSchema->schema_name));
        }

        $connection->statement('DROP SCHEMA IF EXISTS "landlord" CASCADE');
        $connection->statement('CREATE SCHEMA "landlord"');

        $this->databaseManager->purge('landlord');
    }
}
