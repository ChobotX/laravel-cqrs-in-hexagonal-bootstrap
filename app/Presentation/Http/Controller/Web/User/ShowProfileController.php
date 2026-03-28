<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesQuery;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetOwnEffectivePermissions\GetOwnEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetOwnOverrides\GetOwnOverridesQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleAssignmentPolicy;
use App\Domain\Team\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\User\Query\GetOwnProfile\GetOwnProfileQuery;
use Illuminate\View\View;

#[SkipPermissionCheck(reason: 'Profile page is accessible to all authenticated users')]
final readonly class ShowProfileController
{
    /** @param array<string, array{features: array<string, array{actions: list<string>}>}> $availableModules */
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private array $availableModules,
    ) {}

    public function __invoke(): View
    {
        $userId = $this->authenticatedUser->id() ?? '';

        $user = $this->queryBus->dispatch(new GetOwnProfileQuery($userId));
        $effectivePermissions = $this->queryBus->dispatch(new GetOwnEffectivePermissionsQuery($userId));
        $modules = $this->queryBus->dispatch(new GetAvailableModulesQuery);
        $userOverrides = $this->queryBus->dispatch(new GetOwnOverridesQuery($userId));

        $canEditEmail = $this->authorizationChecker->can($userId, 'users.list.update');

        $canManageRoles = $this->authorizationChecker->can($userId, 'users.roles.read');
        $assignableRoles = [];
        $userRoleIds = [];

        if ($canManageRoles) {
            $allRoles = $this->queryBus->dispatch(new ListRolesQuery);
            $userRoles = $this->queryBus->dispatch(new GetUserRolesQuery($userId));
            $assignerPermissions = $this->queryBus->dispatch(new GetEffectivePermissionsQuery($userId));

            $isSuperAdmin = array_any($assignerPermissions, fn ($p): bool => $p->source === 'system:super-admin');

            $roleAssignmentPolicy = new RoleAssignmentPolicy;
            $assignableRoles = $roleAssignmentPolicy->assignableRoles($assignerPermissions, $allRoles, $this->availableModules, $isSuperAdmin);
            $userRoleIds = array_map(fn (Role $role): string => $role->id->value, $userRoles);
        }

        $canManageTeams = $this->authorizationChecker->can($userId, 'teams.members.update');
        $userTeams = $canManageTeams
            ? $this->queryBus->dispatch(new GetUserTeamsQuery($userId))
            : [];

        $canManageOverrides = $this->authorizationChecker->can($userId, 'users.roles.update');

        return view('profile.edit', [
            'user' => $user,
            'canEditEmail' => $canEditEmail,
            'canManageRoles' => $canManageRoles,
            'assignableRoles' => $assignableRoles,
            'userRoleIds' => $userRoleIds,
            'canManageTeams' => $canManageTeams,
            'userTeams' => $userTeams,
            'effectivePermissions' => $effectivePermissions,
            'modules' => $modules,
            'userOverrides' => $userOverrides,
            'canManageOverrides' => $canManageOverrides,
        ]);
    }
}
