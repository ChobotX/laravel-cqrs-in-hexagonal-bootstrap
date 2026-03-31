<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Domain\Authorization\Query\GetAssignableRoles\GetAssignableRolesQuery;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesQuery;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetUserOverrides\GetUserOverridesQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Label\Label;
use App\Domain\Label\Query\GetEntityLabels\GetEntityLabelsQuery;
use App\Domain\Team\Query\GetUserTeams\GetUserTeamsQuery;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use Illuminate\View\View;

#[RequiresPermission('users.list.update')]
final readonly class ShowEditUserController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
    ) {}

    public function __invoke(string $userId): View
    {
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $user = $this->queryBus->dispatch(new GetUserByIdQuery($userId));

        $canManageRoles = $this->authorizationChecker->can($currentUserId, 'users.roles.read');

        $assignableRoles = [];
        $userRoleIds = [];

        if ($canManageRoles) {
            $assignableRoles = $this->queryBus->dispatch(new GetAssignableRolesQuery($currentUserId));
            $userRoles = $this->queryBus->dispatch(new GetUserRolesQuery($userId));
            $userRoleIds = array_map(fn (Role $role): string => $role->id->value, $userRoles);
        }

        $canManageTeams = $this->authorizationChecker->can($currentUserId, 'teams.members.update');

        $userTeams = [];

        if ($canManageTeams) {
            $userTeams = $this->queryBus->dispatch(new GetUserTeamsQuery($userId));
        }

        $canManageLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.read');
        $canCreateLabels = $this->authorizationChecker->can($currentUserId, 'labels.management.create');

        $userLabels = [];

        if ($canManageLabels) {
            /** @var list<Label> $labels */
            $labels = $this->queryBus->dispatch(new GetEntityLabelsQuery($userId));
            $userLabels = array_map(fn (Label $label): array => [
                'id' => $label->id->value,
                'name' => $label->name->value,
            ], $labels);
        }

        $canViewPermissions = $canManageRoles;
        $effectivePermissions = [];
        $modules = [];
        $userOverrides = [];
        $canManageOverrides = false;

        if ($canViewPermissions) {
            $effectivePermissions = $this->queryBus->dispatch(new GetEffectivePermissionsQuery($userId));
            $modules = $this->queryBus->dispatch(new GetAvailableModulesQuery);
            $userOverrides = $this->queryBus->dispatch(new GetUserOverridesQuery($userId));
            $canManageOverrides = $this->authorizationChecker->can($currentUserId, 'users.roles.update');
        }

        return view('users.edit', [
            'user' => $user,
            'canManageRoles' => $canManageRoles,
            'assignableRoles' => $assignableRoles,
            'userRoleIds' => $userRoleIds,
            'canManageTeams' => $canManageTeams,
            'userTeams' => $userTeams,
            'canViewPermissions' => $canViewPermissions,
            'effectivePermissions' => $effectivePermissions,
            'modules' => $modules,
            'userOverrides' => $userOverrides,
            'canManageOverrides' => $canManageOverrides,
            'canManageLabels' => $canManageLabels,
            'canCreateLabels' => $canCreateLabels,
            'userLabels' => $userLabels,
        ]);
    }
}
