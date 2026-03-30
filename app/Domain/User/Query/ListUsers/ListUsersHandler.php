<?php

declare(strict_types=1);

namespace App\Domain\User\Query\ListUsers;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\User;
use App\Domain\User\UserRepository;

/** @implements QueryHandler<ListUsersQuery, list<User>> */
final readonly class ListUsersHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * @return list<User>
     */
    public function handle(Query $query): array
    {
        return $this->userRepository->all($query->accessContext()?->visibleIds);
    }
}
