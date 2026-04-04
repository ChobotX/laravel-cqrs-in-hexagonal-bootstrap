<?php

declare(strict_types=1);

namespace App\Presentation\Console\Tenancy;

use App\Application\Bus\CommandBus;
use App\Application\Tenancy\TenantAgnosticCommand;
use App\Domain\Tenancy\Contract\Command\CreateTenantCommand as CreateTenant;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

#[TenantAgnosticCommand]
final class CreateTenantCommand extends Command
{
    use StrictArguments;

    protected $signature = 'tenant:create
        {name : Tenant display name}
        {slug : Unique tenant slug (used for schema naming)}
        {--domain= : Subdomain for this tenant}';

    protected $description = 'Create a new tenant with schema and run migrations';

    public function handle(CommandBus $commandBus): int
    {
        $name = $this->stringArgument('name');
        $slug = $this->stringArgument('slug');
        $domain = $this->nullableStringOption('domain');

        $this->info(sprintf('Creating tenant "%s" (slug: %s)...', $name, $slug));

        $commandBus->dispatch(new CreateTenant(
            name: $name,
            slug: $slug,
            domain: $domain,
        ));

        $this->info(sprintf('Tenant "%s" created successfully.', $name));

        return self::SUCCESS;
    }
}
