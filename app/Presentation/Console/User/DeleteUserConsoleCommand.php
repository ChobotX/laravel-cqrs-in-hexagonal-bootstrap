<?php

declare(strict_types=1);

namespace App\Presentation\Console\User;

use App\Application\Bus\CommandBus;
use App\Application\Tenancy\TenantAwareCommand;
use App\Domain\User\Command\DeleteUser\DeleteUserCommand;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

#[TenantAwareCommand]
final class DeleteUserConsoleCommand extends Command
{
    use StrictArguments;

    protected $signature = 'user:delete {id} {--tenant= : Tenant slug}';

    protected $description = 'Delete a user';

    public function handle(CommandBus $commandBus): int
    {
        try {
            $commandBus->dispatch(new DeleteUserCommand(
                id: $this->stringArgument('id'),
            ));
        } catch (UserNotFoundException $userNotFoundException) {
            $this->error($userNotFoundException->getMessage());

            return self::FAILURE;
        }

        $this->info('User deleted.');

        return self::SUCCESS;
    }
}
