<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Exception\UserNotFoundException;
use App\Domain\User\Contract\Query\GetUserByIdQuery;
use App\Domain\User\Contract\Repository\UserRepository;
use App\Domain\User\Contract\ValueObject\UserId;

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
