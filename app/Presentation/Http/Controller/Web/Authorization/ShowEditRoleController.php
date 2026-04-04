<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Contract\Query\GetAvailableModulesQuery;
use App\Domain\Authorization\Contract\Query\GetRoleByIdQuery;
use Illuminate\View\View;

#[RequiresPermission('users.roles.update')]
final readonly class ShowEditRoleController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $roleId): View
    {
        $role = $this->queryBus->dispatch(new GetRoleByIdQuery($roleId));
        $modules = $this->queryBus->dispatch(new GetAvailableModulesQuery);

        return view('roles.edit', ['role' => $role, 'modules' => $modules]);
    }
}
