<?php

declare(strict_types=1);

namespace App\Presentation\Console\Tenancy;

use App\Application\Tenancy\TenantAgnosticCommand;
use App\Infrastructure\Tenancy\TenantMigrator;
use App\Infrastructure\Tenancy\TenantResolver;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

#[TenantAgnosticCommand]
final class MigrateCommand extends Command
{
    use StrictArguments;

    protected $signature = 'tenant:migrate {--tenant= : Specific tenant slug (omit for all tenants)}';

    protected $description = 'Run tenant migrations for one or all active tenants';

    public function handle(TenantMigrator $migrator, TenantResolver $resolver): int
    {
        /** @var string|null $slug */
        $slug = $this->option('tenant');

        if ($slug !== null) {
            $tenant = $resolver->resolveBySlug($slug);
            $this->info(sprintf('Migrating tenant "%s" (schema: %s)...', $tenant->name, $tenant->schema_name));
            $migrator->setupTenant($tenant);

            return self::SUCCESS;
        }

        $this->info('Migrating all active tenants...');
        $migrator->migrateAll();
        $this->info('All tenants migrated.');

        return self::SUCCESS;
    }
}
