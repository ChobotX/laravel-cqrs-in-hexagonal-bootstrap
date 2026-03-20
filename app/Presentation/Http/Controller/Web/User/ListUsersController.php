<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Organization\OrganizationContext;
use App\Contract\Organization\TeamMembershipChecker;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Organization\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\Organization\Team;
use App\Domain\User\Query\ListUsers\ListUsersQuery;
use App\Domain\User\User;
use Illuminate\View\View;

#[RequiresPermission('users.list.read')]
final readonly class ListUsersController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    public function __invoke(): View
    {
        $orgId = $this->organizationContext->currentOrganizationId();
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $allUsers = $this->queryBus->dispatch(new ListUsersQuery);
        $users = $this->filterByScope($allUsers, $currentUserId, $orgId);

        $canReadRoles = $this->authorizationChecker->can($currentUserId, $orgId, 'users.roles.read');

        $userRoles = $canReadRoles ? $this->buildUserRolesMap($users, $orgId) : [];
        $isSuperAdmin = false;

        if ($canReadRoles) {
            $currentUserRoles = $this->queryBus->dispatch(new GetUserRolesQuery($currentUserId, $orgId));
            $isSuperAdmin = array_any($currentUserRoles, fn (Role $role): bool => $role->isSystem);
        }

        $canReadTeams = $this->authorizationChecker->can($currentUserId, $orgId, 'teams.members.read');
        $userTeams = $canReadTeams ? $this->buildUserTeamsMap($users, $orgId) : [];

        return view('users.index', [
            'users' => $users,
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
    private function buildUserRolesMap(array $users, string $orgId): array
    {
        $map = [];

        foreach ($users as $user) {
            $map[$user->id->value] = $this->queryBus->dispatch(
                new GetUserRolesQuery($user->id->value, $orgId),
            );
        }

        return $map;
    }

    /**
     * @param  list<User>  $users
     * @return array<string, list<Team>>
     */
    private function buildUserTeamsMap(array $users, string $orgId): array
    {
        $map = [];

        foreach ($users as $user) {
            $map[$user->id->value] = $this->queryBus->dispatch(
                new GetUserTeamsQuery($user->id->value, $orgId),
            );
        }

        return $map;
    }

    /**
     * @param  list<User>  $allUsers
     * @return list<User>
     */
    private function filterByScope(array $allUsers, string $currentUserId, string $orgId): array
    {
        $accessDecision = $this->authorizationChecker->canWithScope($currentUserId, $orgId, 'users.list.read');

        return match ($accessDecision->scope()) {
            'all' => $allUsers,
            'team' => $this->filterByTeamScope($allUsers, $currentUserId, $orgId),
            'own' => array_values(array_filter(
                $allUsers,
                fn (User $user): bool => $user->id->value === $currentUserId,
            )),
            default => $allUsers,
        };
    }

    /**
     * @param  list<User>  $allUsers
     * @return list<User>
     */
    private function filterByTeamScope(array $allUsers, string $currentUserId, string $orgId): array
    {
        $visibleUserIds = $this->teamMembershipChecker->visibleUserIds($currentUserId, $orgId);

        return array_values(array_filter(
            $allUsers,
            fn (User $user): bool => in_array($user->id->value, $visibleUserIds, true),
        ));
    }
}
