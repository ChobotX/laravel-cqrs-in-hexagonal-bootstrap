<?php

declare(strict_types=1);

namespace App\Domain\User\Query\GetUserByEmail;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\UserRepository;
use App\Domain\User\User;

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
