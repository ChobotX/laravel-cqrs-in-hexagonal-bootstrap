<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Presentation\Http\Request\Authorization\UpdateRoleRequest;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class UpdateRoleController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(UpdateRoleRequest $updateRoleRequest): JsonResponse
    {
        $this->commandBus->dispatch($updateRoleRequest->toCommand());

        return new JsonResponse;
    }
}
