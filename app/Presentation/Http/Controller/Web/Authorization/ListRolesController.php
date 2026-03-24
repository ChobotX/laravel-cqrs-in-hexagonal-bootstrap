<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Query\ListRoles\ListRolesQuery;
use Illuminate\View\View;

#[RequiresPermission('users.roles.read')]
final readonly class ListRolesController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        $roles = $this->queryBus->dispatch(new ListRolesQuery);

        return view('roles.index', ['roles' => $roles]);
    }
}
