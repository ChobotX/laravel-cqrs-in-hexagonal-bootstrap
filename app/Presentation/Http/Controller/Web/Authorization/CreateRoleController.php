<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\Authorization;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Presentation\Http\Request\Web\Authorization\CreateRoleRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.roles.update')]
final readonly class CreateRoleController
{
    public function __construct(
        private CommandBus $commandBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(CreateRoleRequest $createRoleRequest): RedirectResponse
    {
        $this->commandBus->dispatch($createRoleRequest->toCommand($this->idGenerator->generate()));

        return redirect('/roles')->with('success', __('messages.roles.created'));
    }
}
