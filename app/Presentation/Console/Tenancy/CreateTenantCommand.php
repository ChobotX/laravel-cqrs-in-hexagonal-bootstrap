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

    public function handle(TenantMigrator $migrator): int
    {
        /** @var string $name */
        $name = $this->argument('name');

        /** @var string $slug */
        $slug = $this->argument('slug');

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

        $tenant = TenantModel::create([
            'id' => Str::uuid()->toString(),
            'name' => $name,
            'slug' => $slug,
            'schema_name' => 'tenant_'.$slug,
            'database_host' => $dbHost,
            'database_port' => $dbPort,
            'database_name' => $dbName,
            'database_username' => $dbUser,
            'database_password' => $dbPass,
            'is_active' => true,
            'config' => [],
        ]);

        /** @var string|null $domain */
        $domain = $this->option('domain');

        if ($domain !== null) {
            TenantDomainModel::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenant->id,
                'domain' => $domain,
                'is_primary' => true,
            ]);
        }

        $this->info(sprintf('Creating schema "%s"...', $tenant->schema_name));
        $migrator->setupTenant($tenant);

        $this->info(sprintf('Tenant "%s" created successfully.', $name));

        return self::SUCCESS;
    }
}
