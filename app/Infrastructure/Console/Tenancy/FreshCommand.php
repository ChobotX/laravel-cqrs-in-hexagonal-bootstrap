<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Tenancy;

use App\Application\Tenancy\TenantAgnosticCommand;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\TenantSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as Artisan;
use Illuminate\Database\DatabaseManager;

#[TenantAgnosticCommand]
final class FreshCommand extends Command
{
    protected $signature = 'tenant:fresh';

    protected $description = 'Drop all tenant and landlord schemas, then run tenant:setup from scratch';

    public function handle(
        TenantSchemaManager $tenantSchemaManager,
        DatabaseManager $databaseManager,
        Artisan $artisan,
    ): int {
        $this->warn('Dropping all schemas...');

        foreach (TenantModel::all() as $tenant) {
            $tenantSchemaManager->dropSchema($tenant->schema_name);
            $this->line(sprintf('  Dropped schema: %s', $tenant->schema_name));
        }

        $connection = $databaseManager->connection('landlord');
        $connection->statement('DROP SCHEMA IF EXISTS landlord CASCADE');
        $connection->statement('CREATE SCHEMA landlord');
        $this->line('  Dropped and recreated schema: landlord');

        $this->info('Running tenant:setup...');
        $artisan->call('tenant:setup', [], $this->output);

        return self::SUCCESS;
    }
}
