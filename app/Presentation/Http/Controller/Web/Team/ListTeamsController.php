<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Team;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Label\Label;
use App\Domain\Label\Query\GetEntityLabels\GetEntityLabelsQuery;
use App\Domain\Team\Query\ListTeams\ListTeamsQuery;
use App\Domain\Team\Team;
use App\Presentation\Http\Request\Web\PaginationRequest;
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

    public function __invoke(PaginationRequest $paginationRequest): View
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $defaultSorting = new Sorting('name', SortDirection::Asc);
        $requestSorting = $paginationRequest->sorting();
        $sorting = $requestSorting instanceof Sorting && in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)
            ? $requestSorting
            : $defaultSorting;

        $paginatedResult = $this->queryBus->dispatch(
            new ListTeamsQuery(
                $currentUserId,
                $paginationRequest->pagination(),
            )->withSorting([$sorting]),
        );

        $canReadLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.read');
        $teamLabels = $canReadLabels ? $this->buildTeamLabelsMap($paginatedResult->items) : [];

        return view('teams.index', [
            'result' => $paginatedResult,
            'sorting' => $sorting,
            'canReadLabels' => $canReadLabels,
            'teamLabels' => $teamLabels,
        ]);
    }

    /**
     * @param  list<Team>  $teams
     * @return array<string, list<Label>>
     */
    private function buildTeamLabelsMap(array $teams): array
    {
        $map = [];

        foreach ($teams as $team) {
            $map[$team->id->value] = $this->queryBus->dispatch(
                new GetEntityLabelsQuery($team->id->value),
            );
        }

        return $map;
    }
}
