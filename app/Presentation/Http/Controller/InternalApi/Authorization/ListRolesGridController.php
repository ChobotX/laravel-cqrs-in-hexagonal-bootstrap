<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\Authorization;

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use App\Application\Pagination\PaginatedResult;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Query\ListRolesQuery;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('users.roles.read')]
final readonly class ListRolesGridController
{
    private const array SORTABLE_COLUMNS = ['name'];

    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): JsonResponse
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';
        $paginatedResult = $this->fetchRoles($paginationRequest);

        return new JsonResponse([
            'data' => array_map(fn (Role $role): array => [
                'id' => $role->id->value,
                'name' => $role->name->value,
                'description' => $role->description,
                'is_system' => $role->isSystem,
                'show_url' => route('roles.show', $role->id->value),
                'edit_url' => route('roles.edit', $role->id->value),
                'delete_url' => route('roles.destroy', $role->id->value),
            ], $paginatedResult->items),
            'meta' => [
                'current_page' => $paginatedResult->pagination->page,
                'per_page' => $paginatedResult->pagination->perPage,
                'total' => $paginatedResult->total,
                'total_pages' => $paginatedResult->totalPages(),
            ],
            'permissions' => [
                'can_read' => true,
                'can_update' => $this->authorizationChecker->can($currentUserId, 'users.roles.update'),
            ],
        ]);
    }

    /** @return PaginatedResult<Role> */
    private function fetchRoles(PaginationRequest $paginationRequest): PaginatedResult
    {
        $sorting = $this->resolveSorting($paginationRequest);
        $search = $paginationRequest->search();
        $filters = $search !== '' ? [new Filter('', FilterOperator::Search, $search)] : [];

        return $this->queryBus->dispatch(
            new ListRolesQuery($paginationRequest->pagination())->withSorting([$sorting])->withFilters($filters),
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
}
