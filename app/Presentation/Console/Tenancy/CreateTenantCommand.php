<?php

declare(strict_types=1);

namespace App\Presentation\Console\Tenancy;

use App\Application\Tenancy\TenantAgnosticCommand;
use App\Infrastructure\Eloquent\Tenancy\TenantDomainModel;
use App\Infrastructure\Eloquent\Tenancy\TenantModel;
use App\Infrastructure\Tenancy\TenantMigrator;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[TenantAgnosticCommand]
final class CreateTenantCommand extends Command
{
    use StrictArguments;

    protected $signature = 'tenant:create
        {name : Tenant display name}
        {slug : Unique tenant slug (used for schema naming)}
        {--domain= : Subdomain for this tenant}';

    protected $description = 'Create a new tenant with schema and run migrations';

    public function handle(TenantMigrator $tenantMigrator): int
    {
        $name = $this->stringArgument('name');
        $slug = $this->stringArgument('slug');

        /** @var array{host: string, port: string|int, database: string, username: string, password: string} $cfg */
        $cfg = config('database.connections.tenant');

        $tenant = TenantModel::create([
            'id' => Str::uuid()->toString(),
            'name' => $name,
            'slug' => $slug,
            'schema_name' => 'tenant_'.$slug,
            'database_host' => $cfg['host'],
            'database_port' => (int) $cfg['port'],
            'database_name' => $cfg['database'],
            'database_username' => $cfg['username'],
            'database_password' => $cfg['password'],
            'is_active' => true,
            'config' => [],
        ]);

        $domain = $this->nullableStringOption('domain');

        if ($domain !== null) {
            TenantDomainModel::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenant->id,
                'domain' => $domain,
                'is_primary' => true,
            ]);
        }

        $this->info(sprintf('Creating schema "%s"...', $tenant->schema_name));
        $tenantMigrator->setupTenant($tenant);

        $this->info(sprintf('Tenant "%s" created successfully.', $name));

        return self::SUCCESS;
    }
}
