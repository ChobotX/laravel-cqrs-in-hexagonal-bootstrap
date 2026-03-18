<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\Authorization\CreateRoleRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class CreateRoleController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(CreateRoleRequest $createRoleRequest): JsonResponse
    {
        $createRoleCommand = $createRoleRequest->toCommand();

        $this->commandBus->dispatch($createRoleCommand);

        return new JsonResponse(['id' => $createRoleCommand->id], Response::HTTP_CREATED);
    }
}
