<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Query\GetOwnOverridesQuery;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;
use App\Domain\Authorization\Contract\ValueObject\UserPermissionOverride;

/** @implements QueryHandler<GetOwnOverridesQuery, list<UserPermissionOverride>> */
final readonly class GetOwnOverridesHandler implements QueryHandler
{
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
    ) {}

    /** @return list<UserPermissionOverride> */
    public function handle(Query $query): array
    {
        return $this->userPermissionRepository->userOverrides($query->userId);
    }
}
