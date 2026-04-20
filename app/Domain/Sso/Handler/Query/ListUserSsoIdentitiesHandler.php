<?php

declare(strict_types=1);

namespace App\Domain\Sso\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Sso\Contract\Entity\UserSsoIdentity;
use App\Domain\Sso\Contract\Query\ListUserSsoIdentitiesQuery;
use App\Domain\Sso\Contract\Repository\UserSsoIdentityRepository;
use App\Domain\User\Contract\ValueObject\UserId;

/** @implements QueryHandler<ListUserSsoIdentitiesQuery, list<UserSsoIdentity>> */
final readonly class ListUserSsoIdentitiesHandler implements QueryHandler
{
    public function __construct(
        private UserSsoIdentityRepository $userSsoIdentityRepository,
    ) {}

    /** @return list<UserSsoIdentity> */
    public function handle(Query $query): array
    {
        return $this->userSsoIdentityRepository->listForUser(new UserId($query->userId));
    }
}
