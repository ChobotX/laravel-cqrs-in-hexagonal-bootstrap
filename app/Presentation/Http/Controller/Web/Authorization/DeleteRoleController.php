<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Domain\Authorization\Contract\Command\DeleteRoleCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.roles.update')]
final readonly class DeleteRoleController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $roleId): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteRoleCommand($roleId));

        return redirect('/roles')->with('success', __('messages.roles.deleted'));
    }
}
