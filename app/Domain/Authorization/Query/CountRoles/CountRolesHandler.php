<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\CountRoles;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\RoleRepository;

/** @implements QueryHandler<CountRolesQuery, int> */
final readonly class CountRolesHandler implements QueryHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ) {}

    public function handle(Query $query): int
    {
        return $this->roleRepository->count();
    }
}
