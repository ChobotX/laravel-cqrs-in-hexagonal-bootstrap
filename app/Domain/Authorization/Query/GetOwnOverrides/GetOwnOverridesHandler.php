<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetOwnOverrides;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\UserPermissionOverride;
use App\Domain\Authorization\UserPermissionRepository;

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
