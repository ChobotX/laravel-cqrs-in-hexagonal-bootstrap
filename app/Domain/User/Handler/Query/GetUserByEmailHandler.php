<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\GetUserByEmailQuery;
use App\Domain\User\Contract\Repository\UserRepository;

/** @implements QueryHandler<GetUserByEmailQuery, ?User> */
final readonly class GetUserByEmailHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function handle(Query $query): ?User
    {
        return $this->userRepository->findByEmail($query->email);
    }
}
