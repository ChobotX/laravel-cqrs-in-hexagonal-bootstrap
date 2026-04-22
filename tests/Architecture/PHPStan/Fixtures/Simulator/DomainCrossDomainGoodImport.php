<?php

declare(strict_types=1);

namespace App\Domain\PhpStanFixtures\Simulator;

use App\Domain\User\Contract\Command\CreateUserCommand;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByIdQuery;

final readonly class DomainCrossDomainGoodImport
{
    public function __construct(
        private CreateUserCommand $createUserCommand,
        private GetUserByIdQuery $getUserByIdQuery,
        private User $user,
    ) {}
}
