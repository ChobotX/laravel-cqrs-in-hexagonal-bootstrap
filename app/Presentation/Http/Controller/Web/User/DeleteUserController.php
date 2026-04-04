<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\User\Contract\Command\DeleteUserCommand;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.list.delete')]
final readonly class DeleteUserController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $userId): RedirectResponse
    {
        $this->commandBus->dispatch(new DeleteUserCommand($userId));

        return redirect('/users')->with('success', __('messages.users.deleted'));
    }
}
