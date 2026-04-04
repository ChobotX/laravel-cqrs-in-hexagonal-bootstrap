<?php

declare(strict_types=1);

namespace App\Presentation\Console\User;

use App\Application\Bus\QueryBus;
use App\Application\Tenancy\TenantAwareCommand;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

#[TenantAwareCommand]
final class GetUserConsoleCommand extends Command
{
    use StrictArguments;

    protected $signature = 'user:get {id} {--tenant= : Tenant slug}';

    protected $description = 'Get a user by ID';

    public function handle(QueryBus $queryBus): int
    {
        try {
            $user = $queryBus->dispatch(new GetUserByIdQuery($this->stringArgument('id')));
        } catch (UserNotFoundException $userNotFoundException) {
            $this->error($userNotFoundException->getMessage());

            return self::FAILURE;
        }

        $this->table(
            ['ID', 'Name', 'Email'],
            [[$user->id->value, $user->name->value, $user->email->value]],
        );

        return self::SUCCESS;
    }
}
