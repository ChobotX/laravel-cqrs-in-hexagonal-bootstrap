<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\SearchRoles;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Role;

/** @implements QueryHandler<SearchRolesQuery, list<Role>> */
final readonly class SearchRolesHandler implements QueryHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ) {}

    /** @return list<Role> */
    public function handle(Query $query): array
    {
        return $this->roleRepository->search(
            $query->term,
            $query->excludeRoleIds,
        );
    }
}
