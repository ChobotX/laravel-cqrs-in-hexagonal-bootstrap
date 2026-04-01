<?php

declare(strict_types=1);

namespace App\Domain\User\Query\ListUsers;

use App\Application\Pagination\PaginatedResult;
use App\Application\Pagination\Pagination;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\User\Contract\UserRepository;
use App\Domain\User\User;

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

        if ($pagination !== null) {
            return $this->userRepository->allPaginated($pagination, $onlyIds, $sortings);
        }

        $items = $this->userRepository->all($onlyIds, $sortings);

        return new PaginatedResult($items, count($items), new Pagination(1, max(count($items), 1)));
    }
}
