<?php

declare(strict_types=1);

namespace App\Presentation\Console\User;

use App\Application\Bus\CommandBus;
use App\Domain\User\Command\UpdateUser\UpdateUserCommand;
use App\Domain\User\Exception\EmailAlreadyExistsException;
use App\Domain\User\Exception\InvalidUserDataException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

final class UpdateUserConsoleCommand extends Command
{
    use StrictArguments;

    protected $signature = 'user:update {id} {name} {email}';

    protected $description = 'Update an existing user';

    public function handle(CommandBus $commandBus): int
    {
        try {
            $commandBus->dispatch(new UpdateUserCommand(
                id: $this->stringArgument('id'),
                name: $this->stringArgument('name'),
                email: $this->stringArgument('email'),
            ));
        } catch (UserNotFoundException|InvalidUserDataException|EmailAlreadyExistsException $domainException) {
            $this->error($domainException->getMessage());

            return self::FAILURE;
        }

        $this->info('User updated.');

        return self::SUCCESS;
    }
}
