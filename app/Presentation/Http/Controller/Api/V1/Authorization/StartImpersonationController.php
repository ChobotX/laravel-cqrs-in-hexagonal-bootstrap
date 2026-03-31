<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\Authorization\Command\StartImpersonation\StartImpersonationCommand;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class StartImpersonationController
{
    public function __construct(
        private CommandBus $commandBus,
        private Guard $guard,
    ) {}

    public function __invoke(string $userId): JsonResponse
    {
        /** @var string $impersonatorId */
        $impersonatorId = $this->guard->id();

        $this->commandBus->dispatch(new StartImpersonationCommand(
            impersonatorId: $impersonatorId,
            targetUserId: $userId,
        ));

        return new JsonResponse;
    }
}
