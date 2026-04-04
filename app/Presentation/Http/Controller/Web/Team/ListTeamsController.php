<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Label\Contract\Query\GetLabelsForEntitiesQuery;
use App\Domain\Team\Contract\Query\ListTeamsQuery;
use App\Domain\Team\Contract\Team;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsController
{
    private const array SORTABLE_COLUMNS = ['name', 'slug'];

    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): View|RedirectResponse
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $defaultSorting = new Sorting('name', SortDirection::Asc);
        $requestSorting = $paginationRequest->sorting();
        $sorting = $requestSorting instanceof Sorting && in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)
            ? $requestSorting
            : $defaultSorting;

        $paginatedResult = $this->queryBus->dispatch(
            new ListTeamsQuery(
                pagination: $paginationRequest->pagination(),
            )->withSorting([$sorting]),
        );

        if ($paginatedResult->isPageOutOfBounds()) {
            return redirect(url()->current().'?'.http_build_query([...$paginationRequest->query(), 'page' => 1]));
        }

        $teamIds = array_map(fn (Team $team): string => $team->id->value, $paginatedResult->items);

        $canReadLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.read');
        $teamLabels = $canReadLabels ? $this->queryBus->dispatch(new GetLabelsForEntitiesQuery($teamIds)) : [];

        return view('teams.index', [
            'result' => $paginatedResult,
            'sorting' => $sorting,
            'canReadLabels' => $canReadLabels,
            'teamLabels' => $teamLabels,
        ]);
    }
}
