<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesQuery;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Domain\Organization\Query\GetUserOrganizations\GetUserOrganizationsQuery;
use App\Domain\Organization\Query\ListOrganizations\ListOrganizationsQuery;
use App\Domain\User\Query\GetUserById\GetUserByIdQuery;
use Illuminate\View\View;

#[RequiresPermission('users.roles.read')]
final readonly class UserPermissionsController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(string $userId): View
    {
        $orgId = $this->organizationContext->currentOrganizationId() ?? '';

        $user = $this->queryBus->dispatch(new GetUserByIdQuery($userId));
        $userRoles = $this->queryBus->dispatch(new GetUserRolesQuery($userId, $orgId));
        $effectivePermissions = $this->queryBus->dispatch(new GetEffectivePermissionsQuery($userId, $orgId));
        $modules = $this->queryBus->dispatch(new GetAvailableModulesQuery);
        $allRoles = $this->queryBus->dispatch(new ListRolesQuery($orgId));
        $userOrganizations = $this->queryBus->dispatch(new GetUserOrganizationsQuery($userId));
        $allOrganizations = $this->queryBus->dispatch(new ListOrganizationsQuery);

        return view('users.permissions', [
            'user' => $user,
            'userRoles' => $userRoles,
            'effectivePermissions' => $effectivePermissions,
            'modules' => $modules,
            'allRoles' => $allRoles,
            'userOrganizations' => $userOrganizations,
            'allOrganizations' => $allOrganizations,
        ]);
    }
}
