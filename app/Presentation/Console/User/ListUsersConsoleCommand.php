<?php

declare(strict_types=1);

namespace App\Presentation\Console\User;

use App\Application\Bus\QueryBus;
use App\Application\Tenancy\TenantAwareCommand;
use App\Domain\User\Contract\Query\ListUsers\ListUsersQuery;
use App\Domain\User\Contract\User;
use Illuminate\Console\Command;

#[TenantAwareCommand]
final class ListUsersConsoleCommand extends Command
{
    protected $signature = 'user:list {--tenant= : Tenant slug}';

    protected $description = 'List all users';

    public function handle(QueryBus $queryBus): void
    {
        $paginatedResult = $queryBus->dispatch(new ListUsersQuery);

        if ($paginatedResult->items === []) {
            $this->info('No users found.');

            return;
        }

        $this->table(
            ['ID', 'Name', 'Email'],
            array_map(
                static fn (User $user): array => [$user->id->value, $user->name->value, $user->email->value],
                $paginatedResult->items,
            ),
        );
    }
}
