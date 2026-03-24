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

    public function handle(TenantMigrator $tenantMigrator, TenantResolver $tenantResolver): int
    {
        $slug = $this->nullableStringOption('tenant');

        if ($slug !== null) {
            $tenant = $tenantResolver->resolveBySlug($slug);
            $this->info(sprintf('Migrating tenant "%s" (schema: %s)...', $tenant->name, $tenant->schema_name));
            $tenantMigrator->setupTenant($tenant);

            return self::SUCCESS;
        }

        $this->info('Migrating all active tenants...');
        $tenantMigrator->migrateAll();
        $this->info('All tenants migrated.');

        return self::SUCCESS;
    }
}
