<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
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
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): View
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $paginatedResult = $this->queryBus->dispatch(new ListUsersQuery($paginationRequest->pagination()));

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
