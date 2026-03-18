<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\User\Command\DeleteUser\DeleteUserCommand;
use Illuminate\Http\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class DeleteUserController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $userId): Response
    {
        $this->commandBus->dispatch(new DeleteUserCommand($userId));

        return new Response(status: 204);
    }
}
