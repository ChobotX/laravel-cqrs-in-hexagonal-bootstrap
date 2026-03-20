<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Authorization\AuthorizationChecker;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Authorization\Role;
use App\Domain\Authorization\RoleAssignmentPolicy;
use App\Domain\Organization\Query\GetUserOrganizations\GetUserOrganizationsQuery;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use Illuminate\View\View;

#[RequiresPermission('users.list.update')]
final readonly class ShowEditUserController
{
    /** @param array<string, array{features: array<string, array{actions: list<string>}>}> $availableModules */
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
        private AuthenticatedUser $authenticatedUser,
        private AuthorizationChecker $authorizationChecker,
        private array $availableModules,
    ) {}

    public function __invoke(string $userId): View
    {
        $orgId = $this->organizationContext->currentOrganizationId();
        $currentUserId = $this->authenticatedUser->id() ?? '';

        $user = $this->queryBus->dispatch(new GetUserByIdQuery($userId));

        $canManageRoles = $this->authorizationChecker->can($currentUserId, $orgId, 'users.roles.read');

        $assignableRoles = [];
        $userRoleIds = [];

        if ($canManageRoles) {
            $allRoles = $this->queryBus->dispatch(new ListRolesQuery($orgId));
            $userRoles = $this->queryBus->dispatch(new GetUserRolesQuery($userId, $orgId));
            $assignerPermissions = $this->queryBus->dispatch(new GetEffectivePermissionsQuery($currentUserId, $orgId));

            $isSuperAdmin = array_any($assignerPermissions, fn ($p): bool => $p->source === 'system:super-admin');

            $roleAssignmentPolicy = new RoleAssignmentPolicy;
            $assignableRoles = $roleAssignmentPolicy->assignableRoles($assignerPermissions, $allRoles, $this->availableModules, $isSuperAdmin);
            $userRoleIds = array_map(fn (Role $role): string => $role->id->value, $userRoles);
        }

        $canManageOrganizations = $this->authorizationChecker->can($currentUserId, $orgId, 'organizations.members.update');

        $userOrganizations = [];

        if ($canManageOrganizations) {
            $userOrganizations = $this->queryBus->dispatch(new GetUserOrganizationsQuery($userId));
        }

        return view('users.edit', [
            'user' => $user,
            'canManageRoles' => $canManageRoles,
            'assignableRoles' => $assignableRoles,
            'userRoleIds' => $userRoleIds,
            'canManageOrganizations' => $canManageOrganizations,
            'userOrganizations' => $userOrganizations,
        ]);
    }
}
