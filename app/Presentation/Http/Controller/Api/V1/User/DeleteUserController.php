<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\User;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Domain\User\Contract\Command\DeleteUserCommand;
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

        return new Response(status: HttpStatus::NO_CONTENT);
    }
}
