<?php

declare(strict_types=1);

namespace App\Presentation\Console\User;

use App\Contract\Attribute\TenantAwareCommand;
use App\Contract\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

#[TenantAwareCommand]
final class CreateUserConsoleCommand extends Command
{
    use StrictArguments;

    protected $signature = 'user:create {name} {email} {--tenant= : Tenant slug}';

    protected $description = 'Create a new user';

    public function handle(CommandBus $commandBus, IdGenerator $idGenerator): void
    {
        $id = $idGenerator->generate();

        $name = $this->stringArgument('name');
        $email = $this->stringArgument('email');

        $commandBus->dispatch(new CreateUserCommand(
            id: $id,
            name: $name,
            email: $email,
        ));

        $this->info('User created with ID: '.$id);
    }
}
