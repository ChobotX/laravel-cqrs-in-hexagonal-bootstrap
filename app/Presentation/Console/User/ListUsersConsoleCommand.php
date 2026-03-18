<?php

declare(strict_types=1);

namespace App\Presentation\Console\User;

use App\Application\Bus\QueryBus;
use App\Domain\User\Query\ListUsers\ListUsersQuery;
use App\Domain\User\User;
use Illuminate\Console\Command;

final class ListUsersConsoleCommand extends Command
{
    protected $signature = 'user:list';

    protected $description = 'List all users';

    public function handle(QueryBus $queryBus): void
    {
        $users = $queryBus->dispatch(new ListUsersQuery);

        if ($users === []) {
            $this->info('No users found.');

            return;
        }

        $this->table(
            ['ID', 'Name', 'Email'],
            array_map(
                static fn (User $user): array => [$user->id->value, $user->name, $user->email->value],
                $users,
            ),
        );
    }
}
