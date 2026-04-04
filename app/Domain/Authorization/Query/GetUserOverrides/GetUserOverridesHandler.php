<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetUserOverrides;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Query\GetUserOverrides\GetUserOverridesQuery;
use App\Domain\Authorization\Contract\UserPermissionOverride;
use App\Domain\Authorization\Contract\UserPermissionRepository;

/** @implements QueryHandler<GetUserOverridesQuery, list<UserPermissionOverride>> */
final readonly class GetUserOverridesHandler implements QueryHandler
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
