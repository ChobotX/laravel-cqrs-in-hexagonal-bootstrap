<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use Illuminate\View\View;

#[RequiresPermission('users.roles.read')]
final readonly class ListRolesController
{
    public function __construct(
        private QueryBus $queryBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(): View
    {
        $organizationId = $this->organizationContext->currentOrganizationId();

        $roles = $this->queryBus->dispatch(new ListRolesQuery($organizationId));

        return view('roles.index', ['roles' => $roles]);
    }
}
