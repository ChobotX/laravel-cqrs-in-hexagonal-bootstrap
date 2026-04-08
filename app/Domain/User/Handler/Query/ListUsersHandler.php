<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\ListUsersQuery;
use App\Domain\User\Contract\Repository\UserRepository;

/** @implements QueryHandler<ListUsersQuery, PaginatedResult<User>> */
final readonly class ListUsersHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /** @return PaginatedResult<User> */
    public function handle(Query $query): PaginatedResult
    {
        $pagination = $query->pagination();
        $onlyIds = $query->accessContext()?->visibleIds;
        $sortings = $query->sorting() !== [] ? $query->sorting() : [new Sorting('name', SortDirection::Asc)];
        $filters = $query->filters();

        if ($pagination !== null) {
            return $this->userRepository->allPaginated($pagination, $onlyIds, $sortings, $filters);
        }

        $items = $this->userRepository->all($onlyIds, $sortings, $filters);

        return new PaginatedResult($items, count($items), new Pagination(1, max(count($items), 1)));
    }
}
