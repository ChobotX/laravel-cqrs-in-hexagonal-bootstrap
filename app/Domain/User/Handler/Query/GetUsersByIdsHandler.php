<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUsersByIdsQuery;
use App\Domain\User\Contract\Repository\UserRepository;

/** @implements QueryHandler<GetUsersByIdsQuery, list<User>> */
final readonly class GetUsersByIdsHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /** @return list<User> */
    public function handle(Query $query): array
    {
        if ($query->userIds === []) {
            return [];
        }

        return $this->userRepository->all($query->userIds);
    }
}
