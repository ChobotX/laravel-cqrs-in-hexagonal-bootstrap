<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\QueryBus;
use App\Domain\Authorization\Contract\Query\GetAvailableModules\GetAvailableModulesQuery;
use Illuminate\View\View;

#[RequiresPermission('users.roles.update')]
final readonly class ShowCreateRoleController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(): View
    {
        $modules = $this->queryBus->dispatch(new GetAvailableModulesQuery);

        return view('roles.create', ['modules' => $modules]);
    }
}
