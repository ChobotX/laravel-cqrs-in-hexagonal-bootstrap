<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\Web\Authorization\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.roles.update')]
final readonly class UpdateRoleController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateRoleRequest $updateRoleRequest): RedirectResponse
    {
        $this->commandBus->dispatch($updateRoleRequest->toCommand());

        return redirect('/roles')->with('success', __('messages.roles.updated'));
    }
}
