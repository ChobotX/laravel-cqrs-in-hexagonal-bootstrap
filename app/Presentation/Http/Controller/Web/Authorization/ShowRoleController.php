<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\QueryBus;
use App\Domain\Authorization\Contract\Query\GetAvailableModulesQuery;
use App\Domain\Authorization\Contract\Query\GetRoleByIdQuery;
use Illuminate\View\View;

#[RequiresPermission('users.roles.read')]
final readonly class ShowRoleController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(string $roleId): View
    {
        $role = $this->queryBus->dispatch(new GetRoleByIdQuery($roleId));
        $modules = $this->queryBus->dispatch(new GetAvailableModulesQuery);

        return view('roles.show', ['role' => $role, 'modules' => $modules]);
    }
}
