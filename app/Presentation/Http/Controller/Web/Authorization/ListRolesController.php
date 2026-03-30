<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\View\View;

#[RequiresPermission('users.roles.read')]
final readonly class ListRolesController
{
    private const array SORTABLE_COLUMNS = ['name'];

    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): View
    {
        $defaultSorting = new Sorting('name', SortDirection::Asc);
        $requestSorting = $paginationRequest->sorting();
        $sorting = $requestSorting instanceof Sorting && in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)
            ? $requestSorting
            : $defaultSorting;

        $paginatedResult = $this->queryBus->dispatch(
            new ListRolesQuery($paginationRequest->pagination())->withSorting([$sorting]),
        );

        return view('roles.index', [
            'result' => $paginatedResult,
            'sorting' => $sorting,
        ]);
    }
}
