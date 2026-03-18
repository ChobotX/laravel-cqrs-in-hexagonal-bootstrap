<?php

declare(strict_types=1);

namespace App\Presentation\Console\User;

use App\Application\Bus\QueryBus;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use App\Presentation\Console\Trait\StrictArguments;
use Illuminate\Console\Command;

final class GetUserConsoleCommand extends Command
{
    use StrictArguments;

    protected $signature = 'user:get {id}';

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
            [[$user->id->value, $user->name, $user->email->value]],
        );

        return self::SUCCESS;
    }
}
