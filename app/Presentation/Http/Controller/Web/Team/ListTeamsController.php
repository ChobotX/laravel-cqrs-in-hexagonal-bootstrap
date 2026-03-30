<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Auth\AuthenticatedUser;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\View\View;

#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsController
{
    private const array SORTABLE_COLUMNS = ['name', 'slug'];

    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): View
    {
        $defaultSorting = new Sorting('name', SortDirection::Asc);
        $requestSorting = $paginationRequest->sorting();
        $sorting = $requestSorting instanceof Sorting && in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)
            ? $requestSorting
            : $defaultSorting;

        $paginatedResult = $this->queryBus->dispatch(
            new ListTeamsQuery(
                $this->authenticatedUser->id() ?? '',
                $paginationRequest->pagination(),
            )->withSorting([$sorting]),
        );

        return view('teams.index', [
            'result' => $paginatedResult,
            'sorting' => $sorting,
        ]);
    }
}
