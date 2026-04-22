<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\InternalApi\User;

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use App\Application\Pagination\PaginatedResult;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Query\GetRolesForUsersQuery;
use App\Domain\Authorization\Contract\Query\GetUserRolesQuery;
use App\Domain\Authorization\Contract\Service\AuthorizationChecker;
use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\Query\GetLabelsForEntitiesQuery;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Query\GetTeamsForUsersQuery;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\ListUsersQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\Http\JsonResponse;

#[RequiresPermission('users.list.read')]
final readonly class ListUsersGridController
{
    private const array SORTABLE_COLUMNS = ['name', 'email'];

    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): JsonResponse
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';
        $paginatedResult = $this->fetchUsers($paginationRequest);
        $userIds = array_map(fn (User $user): string => $user->id->value, $paginatedResult->items);

        $canReadRoles = $this->authorizationChecker->can($currentUserId, 'users.roles.read');
        $userRoles = $canReadRoles ? $this->queryBus->dispatch(new GetRolesForUsersQuery($userIds)) : [];
        $isSuperAdmin = $this->checkSuperAdmin($canReadRoles, $currentUserId);

        $canReadTeams = $this->authorizationChecker->can($currentUserId, 'teams.members.read');
        $userTeams = $canReadTeams ? $this->queryBus->dispatch(new GetTeamsForUsersQuery($userIds)) : [];

        $canReadLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.read');
        $userLabels = $canReadLabels ? $this->queryBus->dispatch(new GetLabelsForEntitiesQuery($userIds)) : [];

        return new JsonResponse([
            'data' => array_map(fn (User $user): array => $this->serializeUser(
                $user, $currentUserId, $canReadRoles, $userRoles[$user->id->value] ?? [],
                $canReadTeams, $userTeams[$user->id->value] ?? [],
                $canReadLabels, $userLabels[$user->id->value] ?? [], $isSuperAdmin,
            ), $paginatedResult->items),
            'meta' => [
                'current_page' => $paginatedResult->pagination->page,
                'per_page' => $paginatedResult->pagination->perPage,
                'total' => $paginatedResult->total,
                'total_pages' => $paginatedResult->totalPages(),
            ],
            'permissions' => [
                'can_create' => $this->authorizationChecker->can($currentUserId, 'users.list.create'),
                'can_update' => $this->authorizationChecker->can($currentUserId, 'users.list.update'),
                'can_delete' => $this->authorizationChecker->can($currentUserId, 'users.list.delete'),
                'is_super_admin' => $isSuperAdmin,
            ],
        ]);
    }

    /**
     * @param  list<Role>  $roles
     * @param  list<Team>  $teams
     * @param  list<Label>  $labels
     * @return array<string, mixed>
     */
    private function serializeUser(
        User $user,
        string $currentUserId,
        bool $canReadRoles,
        array $roles,
        bool $canReadTeams,
        array $teams,
        bool $canReadLabels,
        array $labels,
        bool $isSuperAdmin,
    ): array {
        return [
            'id' => $user->id->value,
            'name' => $user->name->value,
            'email' => $user->email->value,
            'avatar_url' => $user->avatarFileId instanceof \App\Domain\File\Contract\ValueObject\FileId ? route('files.show', $user->avatarFileId->value) : null,
            'initials' => strtoupper(substr($user->name->value, 0, 1)).strtoupper(substr($user->name->value, (int) strpos($user->name->value, ' ') + 1, 1)),
            'roles' => $canReadRoles ? array_map(fn (Role $role): array => [
                'id' => $role->id->value,
                'name' => $role->name->value,
                'url' => route('roles.show', $role->id->value),
                'is_system' => $role->isSystem,
            ], $roles) : null,
            'teams' => $canReadTeams ? array_map(fn (Team $team): array => [
                'id' => $team->id->value,
                'name' => $team->name->value,
                'url' => route('teams.show', $team->id->value),
            ], $teams) : null,
            'labels' => $canReadLabels ? array_map(fn (Label $label): array => [
                'id' => $label->id->value,
                'name' => $label->name->value,
            ], $labels) : null,
            'edit_url' => route('users.edit', $user->id->value),
            'delete_url' => route('users.destroy', $user->id->value),
            'impersonate_url' => $isSuperAdmin && $user->id->value !== $currentUserId
                ? route('impersonation.start', $user->id->value) : null,
        ];
    }

    /** @return PaginatedResult<User> */
    private function fetchUsers(PaginationRequest $paginationRequest): PaginatedResult
    {
        $sorting = $this->resolveSorting($paginationRequest);
        $search = $paginationRequest->search();
        $filters = $search !== '' ? [new Filter('', FilterOperator::Search, $search)] : [];

        return $this->queryBus->dispatch(
            new ListUsersQuery($paginationRequest->pagination())->withSorting([$sorting])->withFilters($filters),
        );
    }

    private function checkSuperAdmin(bool $canReadRoles, string $currentUserId): bool
    {
        if (! $canReadRoles) {
            return false;
        }

        $currentUserRoles = $this->queryBus->dispatch(new GetUserRolesQuery($currentUserId));

        return array_any($currentUserRoles, fn (Role $role): bool => $role->isSystem);
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
