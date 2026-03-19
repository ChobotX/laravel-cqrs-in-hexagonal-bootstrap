<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Organization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Organization\Query\ListOrganizations\ListOrganizationsQuery;
use Illuminate\View\View;

#[RequiresPermission('organizations.management.read')]
final readonly class ListOrganizationsController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        $organizations = $this->queryBus->dispatch(new ListOrganizationsQuery);

        return view('organizations.index', ['organizations' => $organizations]);
    }
}
