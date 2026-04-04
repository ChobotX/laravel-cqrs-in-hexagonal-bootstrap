<?php

declare(strict_types=1);

namespace App\Presentation\Console\User;

use App\Application\Bus\CommandBus;
use App\Application\Bus\QueryBus;
use App\Application\Tenancy\TenantAwareCommand;
use App\Domain\User\Contract\Command\SetPassword\SetPasswordCommand;
use App\Domain\User\Contract\Query\GetUserByEmail\GetUserByEmailQuery;
use App\Domain\User\Contract\User;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

#[TenantAwareCommand]
final class SetUserPasswordConsoleCommand extends Command
{
    use StrictArguments;

    protected $signature = 'user:set-password {email} {--tenant= : Tenant slug}';

    protected $description = 'Set password for an existing user';

    public function handle(QueryBus $queryBus, CommandBus $commandBus): int
    {
        $email = $this->stringArgument('email');

        $password = $this->secret('Password:');

        if (! is_string($password) || $password === '') {
            $this->error('Password must not be empty.');

            return self::FAILURE;
        }

        $user = $queryBus->dispatch(new GetUserByEmailQuery($email));

        if (! $user instanceof User) {
            $this->error('User not found with email: '.$email);

            return self::FAILURE;
        }

        $commandBus->dispatch(new SetPasswordCommand(
            userId: $user->id->value,
            rawPassword: $password,
        ));

        $this->info('Password set for user: '.$email);

        return self::SUCCESS;
    }
}
