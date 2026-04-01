<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Query\ListRoles;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\RoleRepository;
use App\Domain\Authorization\Role;

/** @implements QueryHandler<ListRolesQuery, PaginatedResult<Role>> */
final readonly class ListRolesHandler implements QueryHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ) {}

    /** @return PaginatedResult<Role> */
    public function handle(Query $query): PaginatedResult
    {
        $pagination = $query->pagination();
        $sortings = $query->sorting() !== [] ? $query->sorting() : [new Sorting(Sorting::PERMISSION_SCORE, SortDirection::Desc)];

        if ($pagination !== null) {
            return $this->roleRepository->findAllPaginated($pagination, $sortings);
        }

        $items = $this->roleRepository->findAll($sortings);

        return new PaginatedResult($items, count($items), new Pagination(1, max(count($items), 1)));
    }
}
