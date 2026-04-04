<?php

declare(strict_types=1);

namespace App\Domain\User\Query\CountUsers;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Query\CountUsers\CountUsersQuery;
use App\Domain\User\Contract\UserRepository;

/** @implements QueryHandler<CountUsersQuery, int> */
final readonly class CountUsersHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function handle(Query $query): int
    {
        return $this->userRepository->count();
    }
}
