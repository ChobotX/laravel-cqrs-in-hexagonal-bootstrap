<?php

declare(strict_types=1);

namespace App\Presentation\Console\Tenancy;

use App\Application\Tenancy\TenantAgnosticCommand;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as Artisan;

#[TenantAgnosticCommand]
final class SetupCommand extends Command
{
    use StrictArguments;

    protected $signature = 'tenant:setup';

    protected $description = 'Run landlord migrations, create default tenants, run tenant migrations, and seed';

    public function handle(Artisan $artisan): int
    {
        $this->info('Running landlord migrations...');
        $artisan->call('migrate', [
            '--database' => 'landlord',
            '--path' => 'database/migrations/landlord',
            '--force' => true,
        ]);
        $this->line($artisan->output());

        $this->info('Seeding database...');
        $artisan->call('db:seed', ['--force' => true]);
        $this->line($artisan->output());

        $this->info('Setup complete.');

        return self::SUCCESS;
    }
}
