<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Organization\Query\GetOrganizationById\GetOrganizationByIdQuery;
use App\Domain\Organization\Query\ListOrganizationMembers\ListOrganizationMembersQuery;
use Illuminate\View\View;

#[RequiresPermission('organizations.management.read')]
final readonly class ShowOrganizationController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $organizationId): View
    {
        $organization = $this->queryBus->dispatch(new GetOrganizationByIdQuery($organizationId));
        $members = $this->queryBus->dispatch(new ListOrganizationMembersQuery($organizationId));

        return view('organizations.show', [
            'organization' => $organization,
            'members' => $members,
        ]);
    }
}
