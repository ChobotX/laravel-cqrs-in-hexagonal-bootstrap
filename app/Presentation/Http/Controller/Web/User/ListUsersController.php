<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Role;
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
    ) {}

    public function __invoke(): View
    {
        $users = $this->queryBus->dispatch(new ListUsersQuery);
        $orgId = $this->organizationContext->currentOrganizationId();
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $canReadRoles = $this->authorizationChecker->can($currentUserId, $orgId, 'users.roles.read');

        $userRoles = $canReadRoles ? $this->buildUserRolesMap($users, $orgId) : [];
        $isSuperAdmin = false;

        if ($canReadRoles) {
            $currentUserRoles = $this->queryBus->dispatch(new GetUserRolesQuery($currentUserId, $orgId));
            $isSuperAdmin = array_any($currentUserRoles, fn (Role $role): bool => $role->isSystem);
        }

        return view('users.index', [
            'users' => $users,
            'userRoles' => $userRoles,
            'canReadRoles' => $canReadRoles,
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
}
