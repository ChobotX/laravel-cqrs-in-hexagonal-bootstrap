<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\User;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Presentation\Http\Request\User\CreateUserRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class CreateUserController
{
    public function __construct(
        private CommandBus $commandBus,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(CreateUserRequest $createUserRequest): JsonResponse
    {
        $createUserCommand = $createUserRequest->toCommand($this->idGenerator->generate());

        $this->commandBus->dispatch($createUserCommand);

        return new JsonResponse(['id' => $createUserCommand->id], Response::HTTP_CREATED);
    }
}
