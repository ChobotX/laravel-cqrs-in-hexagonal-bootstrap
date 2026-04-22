<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\User;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Auth\AuthenticatedUser;
use App\Contract\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Presentation\Http\Request\Web\User\CreateUserRequest;
use Illuminate\Http\RedirectResponse;

#[RequiresPermission('users.list.create')]
final readonly class CreateUserController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(CreateUserRequest $createUserRequest): RedirectResponse
    {
        $this->commandBus->dispatch($createUserRequest->toCommand(
            $this->idGenerator->generate(),
            $this->authenticatedUser->id() ?? '',
        ));

        return redirect('/users')->with('success', __('messages.users.created'));
    }
}
