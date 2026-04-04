<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Query\GetRolesForUsersQuery;
use App\Domain\Authorization\Contract\Repository\UserPermissionRepository;

/** @implements QueryHandler<GetRolesForUsersQuery, array<string, list<Role>>> */
final readonly class GetRolesForUsersHandler implements QueryHandler
{
    public function __construct(
        private UserPermissionRepository $userPermissionRepository,
    ) {}

    /** @return array<string, list<Role>> */
    public function handle(Query $query): array
    {
        return $this->userPermissionRepository->userRolesForUsers($query->userIds);
    }
}
