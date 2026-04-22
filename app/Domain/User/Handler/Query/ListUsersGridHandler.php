<?php

declare(strict_types=1);

namespace App\Domain\User\Handler\Query;

use App\Application\Filtering\Filter;
use App\Application\Filtering\FilterOperator;
use App\Application\Pagination\PaginatedResult;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Auth\AuthorizationChecker;
use App\Contract\Bus\QueryBus;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Authorization\Contract\Entity\Role;
use App\Domain\Authorization\Contract\Query\GetRolesForUsersQuery;
use App\Domain\Authorization\Contract\Query\GetUserRolesQuery;
use App\Domain\Label\Contract\Entity\Label;
use App\Domain\Label\Contract\Query\GetLabelsForEntitiesQuery;
use App\Domain\Team\Contract\Entity\Team;
use App\Domain\Team\Contract\Query\GetTeamsForUsersQuery;
use App\Domain\User\Contract\Entity\User;
use App\Domain\User\Contract\Query\ListUsersGridQuery;
use App\Domain\User\Contract\Query\ListUsersQuery;
use App\Domain\User\Contract\ValueObject\UserGridLabel;
use App\Domain\User\Contract\ValueObject\UserGridRoleLabel;
use App\Domain\User\Contract\ValueObject\UserGridRow;
use App\Domain\User\Contract\ValueObject\UserGridTeamLabel;
use App\Domain\User\Contract\ValueObject\UsersGridPermissions;
use App\Domain\User\Contract\ValueObject\UsersGridResult;

/** @implements QueryHandler<ListUsersGridQuery, UsersGridResult> */
final readonly class ListUsersGridHandler implements QueryHandler
{
    private const array SORTABLE_COLUMNS = ['name', 'email'];

    public function __construct(
        private QueryBus $queryBus,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function handle(Query $query): UsersGridResult
    {
        $paginatedResult = $this->fetchUsers($query);
        $userIds = array_map(fn (User $user): string => $user->id->value, $paginatedResult->items);

        $canReadRoles = $this->authorizationChecker->can($query->actingUserId, 'users.roles.read');
        $userRoles = $canReadRoles ? $this->queryBus->dispatch(new GetRolesForUsersQuery($userIds)) : [];
        $isSuperAdmin = $this->resolveIsSuperAdmin($canReadRoles, $query->actingUserId);

        $canReadTeams = $this->authorizationChecker->can($query->actingUserId, 'teams.members.read');
        $userTeams = $canReadTeams ? $this->queryBus->dispatch(new GetTeamsForUsersQuery($userIds)) : [];

        $canReadLabels = $this->authorizationChecker->can($query->actingUserId, 'labels.management.read');
        $userLabels = $canReadLabels ? $this->queryBus->dispatch(new GetLabelsForEntitiesQuery($userIds)) : [];

        $rows = array_map(
            fn (User $user): UserGridRow => $this->mapRow(
                $user,
                $query->actingUserId,
                $isSuperAdmin,
                $canReadRoles,
                $userRoles[$user->id->value] ?? [],
                $canReadTeams,
                $userTeams[$user->id->value] ?? [],
                $canReadLabels,
                $userLabels[$user->id->value] ?? [],
            ),
            $paginatedResult->items,
        );

        return new UsersGridResult(
            rows: $rows,
            total: $paginatedResult->total,
            page: $paginatedResult->pagination->page,
            perPage: $paginatedResult->pagination->perPage,
            totalPages: $paginatedResult->totalPages(),
            permissions: new UsersGridPermissions(
                canCreate: $this->authorizationChecker->can($query->actingUserId, 'users.list.create'),
                canUpdate: $this->authorizationChecker->can($query->actingUserId, 'users.list.update'),
                canDelete: $this->authorizationChecker->can($query->actingUserId, 'users.list.delete'),
                isSuperAdmin: $isSuperAdmin,
            ),
        );
    }

    /**
     * @return PaginatedResult<User>
     */
    private function fetchUsers(ListUsersGridQuery $listUsersGridQuery): PaginatedResult
    {
        $sorting = $listUsersGridQuery->sorting instanceof Sorting && in_array($listUsersGridQuery->sorting->column, self::SORTABLE_COLUMNS, true)
            ? $listUsersGridQuery->sorting
            : new Sorting('name', SortDirection::Asc);

        $filters = $listUsersGridQuery->search !== ''
            ? [new Filter('', FilterOperator::Search, $listUsersGridQuery->search)]
            : [];

        return $this->queryBus->dispatch(
            new ListUsersQuery($listUsersGridQuery->pagination)->withSorting([$sorting])->withFilters($filters),
        );
    }

    private function resolveIsSuperAdmin(bool $canReadRoles, string $actingUserId): bool
    {
        if (! $canReadRoles) {
            return false;
        }

        $actingRoles = $this->queryBus->dispatch(new GetUserRolesQuery($actingUserId));

        return array_any($actingRoles, fn (Role $role): bool => $role->isSystem);
    }

    /**
     * @param  list<Role>  $roles
     * @param  list<Team>  $teams
     * @param  list<Label>  $labels
     */
    private function mapRow(
        User $user,
        string $actingUserId,
        bool $isSuperAdmin,
        bool $canReadRoles,
        array $roles,
        bool $canReadTeams,
        array $teams,
        bool $canReadLabels,
        array $labels,
    ): UserGridRow {
        $name = $user->name->value;
        $initials = strtoupper(substr($name, 0, 1))
            .strtoupper(substr($name, (int) strpos($name, ' ') + 1, 1));

        return new UserGridRow(
            id: $user->id->value,
            name: $name,
            email: $user->email->value,
            avatarFileId: $user->avatarFileId?->value,
            initials: $initials,
            roles: $canReadRoles ? array_map(fn (Role $role): UserGridRoleLabel => new UserGridRoleLabel(
                id: $role->id->value,
                name: $role->name->value,
                isSystem: $role->isSystem,
            ), $roles) : null,
            teams: $canReadTeams ? array_map(fn (Team $team): UserGridTeamLabel => new UserGridTeamLabel(
                id: $team->id->value,
                name: $team->name->value,
            ), $teams) : null,
            labels: $canReadLabels ? array_map(fn (Label $label): UserGridLabel => new UserGridLabel(
                id: $label->id->value,
                name: $label->name->value,
            ), $labels) : null,
            impersonable: $isSuperAdmin && $user->id->value !== $actingUserId,
        );
    }
}
