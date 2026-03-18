<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\ListRoles;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleRepository;

/** @implements QueryHandler<ListRolesQuery, list<Role>> */
final readonly class ListRolesHandler implements QueryHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ) {}

    /** @return list<Role> */
    public function handle(Query $query): array
    {
        return $this->roleRepository->findByOrganizationId($query->organizationId);
    }
}
