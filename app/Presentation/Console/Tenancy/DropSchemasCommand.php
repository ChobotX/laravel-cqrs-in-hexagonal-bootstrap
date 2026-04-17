<?php

declare(strict_types=1);

namespace App\Presentation\Console\Tenancy;

use App\Application\Tenancy\TenantAgnosticCommand;
use App\Domain\Tenancy\Contract\Service\DevSchemaResetter;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

#[TenantAgnosticCommand]
final class DropSchemasCommand extends Command
{
    use StrictArguments;

    protected $signature = 'tenant:drop-schemas';

    protected $description = 'Drop all tenant schemas and reset landlord schema (dev only)';

    public function handle(DevSchemaResetter $devSchemaResetter): int
    {
        $devSchemaResetter->resetAll();

        $this->info('All schemas dropped and landlord reset.');

        return self::SUCCESS;
    }
}
