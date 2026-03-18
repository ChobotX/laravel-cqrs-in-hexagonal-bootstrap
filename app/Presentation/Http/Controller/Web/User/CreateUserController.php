<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Application\Authorization\RequiresPermission;
use App\Application\Bus\CommandBus;
use App\Domain\User\Command\SetPassword\SetPasswordCommand;
use App\Presentation\Http\Request\Web\User\CreateUserRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.list.create')]
final readonly class CreateUserController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(CreateUserRequest $createUserRequest): RedirectResponse
    {
        $createUserCommand = $createUserRequest->toCommand();

        $this->commandBus->dispatch($createUserCommand);
        $this->commandBus->dispatch(new SetPasswordCommand($createUserCommand->id, $createUserRequest->string('password')->toString()));

        return redirect('/users')->with('success', __('messages.users.created'));
    }
}
