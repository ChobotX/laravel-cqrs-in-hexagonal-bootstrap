<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Team\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\Team\Team;
use App\Domain\User\Query\ListUsers\ListUsersQuery;
use App\Domain\User\User;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\View\View;

#[RequiresPermission('users.list.read')]
final readonly class ListUsersController
{
    private const array SORTABLE_COLUMNS = ['name', 'email'];

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
            new ListUsersQuery($paginationRequest->pagination())->withSorting([$sorting]),
        );

        $canReadRoles = $this->authorizationChecker->can($currentUserId, 'users.roles.read');

        $userRoles = $canReadRoles ? $this->buildUserRolesMap($paginatedResult->items) : [];
        $isSuperAdmin = false;

        if ($canReadRoles) {
            $currentUserRoles = $this->queryBus->dispatch(new GetUserRolesQuery($currentUserId));
            $isSuperAdmin = array_any($currentUserRoles, fn (Role $role): bool => $role->isSystem);
        }

        $canReadTeams = $this->authorizationChecker->can($currentUserId, 'teams.members.read');
        $userTeams = $canReadTeams ? $this->buildUserTeamsMap($paginatedResult->items) : [];

        return view('users.index', [
            'result' => $paginatedResult,
            'sorting' => $sorting,
            'userRoles' => $userRoles,
            'canReadRoles' => $canReadRoles,
            'canReadTeams' => $canReadTeams,
            'userTeams' => $userTeams,
            'isSuperAdmin' => $isSuperAdmin,
            'currentUserId' => $currentUserId,
        ]);
    }

    /**
     * @param  list<User>  $users
     * @return array<string, list<Role>>
     */
    private function buildUserRolesMap(array $users): array
    {
        $map = [];

        foreach ($users as $user) {
            $map[$user->id->value] = $this->queryBus->dispatch(
                new GetUserRolesQuery($user->id->value),
            );
        }

        return $map;
    }

    /**
     * @param  list<User>  $users
     * @return array<string, list<Team>>
     */
    private function buildUserTeamsMap(array $users): array
    {
        $map = [];

        foreach ($users as $user) {
            $map[$user->id->value] = $this->queryBus->dispatch(
                new GetUserTeamsQuery($user->id->value),
            );
        }

        return $map;
    }
}
