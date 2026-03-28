<?php

declare(strict_types=1);

namespace App\Domain\User\Query\GetOwnProfile;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;

/** @implements QueryHandler<GetOwnProfileQuery, User> */
final readonly class GetOwnProfileHandler implements QueryHandler
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
