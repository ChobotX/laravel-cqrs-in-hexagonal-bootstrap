<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\GetUserRoles;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Role;
use App\Domain\Authorization\Contract\UserPermissionRepository;

/** @implements QueryHandler<GetUserRolesQuery, list<Role>> */
final readonly class GetUserRolesHandler implements QueryHandler
{
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
    ) {}

    /** @return list<Role> */
    public function handle(Query $query): array
    {
        return $this->userPermissionRepository->userRoles($query->userId);
    }
}
