<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Query\GetAvailableModules\GetAvailableModulesQuery;
use App\Domain\Authorization\Query\GetEffectivePermissions\GetEffectivePermissionsQuery;
use App\Domain\Authorization\Query\GetUserOverrides\GetUserOverridesQuery;
use App\Domain\Authorization\Query\GetUserRoles\GetUserRolesQuery;
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
        $orgId = $this->organizationContext->currentOrganizationId();

        $user = $this->queryBus->dispatch(new GetUserByIdQuery($userId));
        $userRoles = $this->queryBus->dispatch(new GetUserRolesQuery($userId, $orgId));
        $effectivePermissions = $this->queryBus->dispatch(new GetEffectivePermissionsQuery($userId, $orgId));
        $modules = $this->queryBus->dispatch(new GetAvailableModulesQuery);
        $userOverrides = $this->queryBus->dispatch(new GetUserOverridesQuery($userId, $orgId));

        return view('users.permissions', [
            'user' => $user,
            'userRoles' => $userRoles,
            'effectivePermissions' => $effectivePermissions,
            'modules' => $modules,
            'userOverrides' => $userOverrides,
        ]);
    }
}
