<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\User;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\User\CreateUserRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class CreateUserController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(CreateUserRequest $createUserRequest): JsonResponse
    {
        $createUserCommand = $createUserRequest->toCommand();

        $this->commandBus->dispatch($createUserCommand);

        return new JsonResponse(['id' => $createUserCommand->id], Response::HTTP_CREATED);
    }
}
