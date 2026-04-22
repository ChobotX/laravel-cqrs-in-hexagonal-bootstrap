<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\Team;

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use App\Application\Pagination\PaginatedResult;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\Query\GetLabelsForEntitiesQuery;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Query\ListTeamsQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('teams.management.read')]
final readonly class ListTeamsGridController
{
    private const array SORTABLE_COLUMNS = ['name', 'slug'];

    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): JsonResponse
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';
        $paginatedResult = $this->fetchTeams($paginationRequest);

        $teamIds = array_map(fn (Team $team): string => $team->id->value, $paginatedResult->items);
        $canReadLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.read');
        $teamLabels = $canReadLabels ? $this->queryBus->dispatch(new GetLabelsForEntitiesQuery($teamIds)) : [];

        $parentMap = $this->buildParentMap($paginatedResult->items);

        return new JsonResponse([
            'data' => array_map(fn (Team $team): array => [
                'id' => $team->id->value,
                'name' => $team->name->value,
                'slug' => $team->slug->value,
                'description' => $team->description,
                'parent_name' => $team->parentTeamId instanceof \App\Domain\Team\Contract\ValueObject\TeamId ? ($parentMap[$team->parentTeamId->value] ?? null) : null,
                'labels' => $canReadLabels ? array_map(fn (Label $label): array => [
                    'id' => $label->id->value,
                    'name' => $label->name->value,
                ], $teamLabels[$team->id->value] ?? []) : null,
                'show_url' => route('teams.show', $team->id->value),
                'edit_url' => route('teams.edit', $team->id->value),
                'delete_url' => route('teams.destroy', $team->id->value),
            ], $paginatedResult->items),
            'meta' => [
                'current_page' => $paginatedResult->pagination->page,
                'per_page' => $paginatedResult->pagination->perPage,
                'total' => $paginatedResult->total,
                'total_pages' => $paginatedResult->totalPages(),
            ],
            'permissions' => [
                'can_create' => $this->authorizationChecker->can($currentUserId, 'teams.management.create'),
                'can_read' => true,
                'can_update' => $this->authorizationChecker->can($currentUserId, 'teams.management.update'),
                'can_delete' => $this->authorizationChecker->can($currentUserId, 'teams.management.delete'),
            ],
        ]);
    }

    /** @return PaginatedResult<Team> */
    private function fetchTeams(PaginationRequest $paginationRequest): PaginatedResult
    {
        $sorting = $this->resolveSorting($paginationRequest);
        $search = $paginationRequest->search();
        $filters = $search !== '' ? [new Filter('', FilterOperator::Search, $search)] : [];

        return $this->queryBus->dispatch(
            new ListTeamsQuery(pagination: $paginationRequest->pagination())->withSorting([$sorting])->withFilters($filters),
        );
    }

    private function resolveSorting(PaginationRequest $paginationRequest): Sorting
    {
        $defaultSorting = new Sorting('name', SortDirection::Asc);
        $requestSorting = $paginationRequest->sorting();

        return $requestSorting instanceof Sorting && in_array($requestSorting->column, self::SORTABLE_COLUMNS, true)
            ? $requestSorting
            : $defaultSorting;
    }

    /**
     * @param  list<Team>  $teams
     * @return array<string, string>
     */
    private function buildParentMap(array $teams): array
    {
        $map = [];

        foreach ($teams as $team) {
            $map[$team->id->value] = $team->name->value;
        }

        return $map;
    }
}
