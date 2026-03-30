<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use App\Presentation\Http\Request\Web\PaginationRequest;
use Illuminate\View\View;

#[RequiresPermission('users.roles.read')]
final readonly class ListRolesController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(PaginationRequest $paginationRequest): View
    {
        $paginatedResult = $this->queryBus->dispatch(new ListRolesQuery($paginationRequest->pagination()));

        return view('roles.index', ['result' => $paginatedResult]);
    }
}
