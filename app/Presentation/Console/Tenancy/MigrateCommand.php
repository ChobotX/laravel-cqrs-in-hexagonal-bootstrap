<?php

declare(strict_types=1);

namespace App\Presentation\Console\Tenancy;

use App\Application\Bus\CommandBus;
use App\Application\Tenancy\TenantAgnosticCommand;
use App\Domain\Tenancy\Contract\Command\MigrateAllTenantsCommand;
use App\Domain\Tenancy\Contract\Command\MigrateTenantCommand;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

#[TenantAgnosticCommand]
final class MigrateCommand extends Command
{
    use StrictArguments;

    protected $signature = 'tenant:migrate {--tenant= : Specific tenant slug (omit for all tenants)}';

    protected $description = 'Run tenant migrations for one or all active tenants';

    public function handle(CommandBus $commandBus): int
    {
        $slug = $this->nullableStringOption('tenant');

        if ($slug !== null) {
            $this->info(sprintf('Migrating tenant "%s"...', $slug));
            $commandBus->dispatch(new MigrateTenantCommand(slug: $slug));

            return self::SUCCESS;
        }

        $this->info('Migrating all active tenants...');
        $commandBus->dispatch(new MigrateAllTenantsCommand);
        $this->info('All tenants migrated.');

        return self::SUCCESS;
    }
}
