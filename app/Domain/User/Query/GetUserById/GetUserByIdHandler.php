<?php

declare(strict_types=1);

namespace App\Domain\User\Query\GetUserById;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\UserId;
use App\Domain\User\Contract\UserRepository;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;

/** @implements QueryHandler<GetUserByIdQuery, User> */
final readonly class GetUserByIdHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function handle(Query $query): User
    {
        $user = $this->userRepository->findById(new UserId($query->id));

        if (! $user instanceof User) {
            throw new UserNotFoundException($query->id);
        }

        return $user;
    }
}
