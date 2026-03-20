<?php

declare(strict_types=1);

namespace App\Domain\User\Query\SearchUsers;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\User;
use App\Domain\User\UserRepository;

/** @implements QueryHandler<SearchUsersQuery, list<User>> */
final readonly class SearchUsersHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * @return list<User>
     */
    public function handle(Query $query): array
    {
        return $this->userRepository->search(
            $query->term,
            $query->restrictToOrganizationIds,
            $query->excludeUserIds,
            $query->limit,
        );
    }
}
