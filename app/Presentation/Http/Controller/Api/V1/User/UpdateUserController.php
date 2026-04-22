<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\User;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Presentation\Http\Request\User\UpdateUserRequest;
use Illuminate\Http\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class UpdateUserController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateUserRequest $updateUserRequest): Response
    {
        $this->commandBus->dispatch($updateUserRequest->toCommand());

        return new Response(status: HttpStatus::NO_CONTENT);
    }
}
