<?php

declare(strict_types=1);

namespace App\Presentation\Console\Tenancy;

use App\Contract\Attribute\TenantAgnosticCommand;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as Artisan;

#[TenantAgnosticCommand]
final class SetupCommand extends Command
{
    use StrictArguments;

    protected $signature = 'tenant:setup';

    protected $description = 'Drop all schemas, run landlord migrations, create default tenants, run tenant migrations, and seed';

    public function handle(Artisan $artisan): int
    {
        $this->info('Dropping existing schemas...');
        $artisan->call('tenant:drop-schemas');
        $this->line($artisan->output());

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

        $this->info('Ensuring public storage symlink...');
        $artisan->call('storage:link');
        $this->line($artisan->output());

        $this->info('Setup complete.');

        return self::SUCCESS;
    }
}
