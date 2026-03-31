<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\Authorization\Command\StopImpersonation\StopImpersonationCommand;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class StopImpersonationController
{
    public function __construct(
        private CommandBus $commandBus,
        private Guard $guard,
    ) {}

    public function __invoke(): JsonResponse
    {
        /** @var string $impersonatorId */
        $impersonatorId = $this->guard->id();

        $this->commandBus->dispatch(new StopImpersonationCommand(
            impersonatorId: $impersonatorId,
        ));

        return new JsonResponse;
    }
}
