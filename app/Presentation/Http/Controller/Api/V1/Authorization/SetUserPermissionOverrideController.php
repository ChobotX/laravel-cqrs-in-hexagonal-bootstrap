<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\Authorization\Contract\Command\SetPermissionOverride\SetPermissionOverrideCommand;
use App\Presentation\Http\Request\Authorization\SetPermissionOverrideRequest;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class SetUserPermissionOverrideController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(SetPermissionOverrideRequest $setPermissionOverrideRequest, string $userId): JsonResponse
    {
        $this->commandBus->dispatch(new SetPermissionOverrideCommand(
            userId: $userId,
            permission: $setPermissionOverrideRequest->string('permission')->toString(),
            type: $setPermissionOverrideRequest->string('type')->toString(),
            scope: $setPermissionOverrideRequest->string('scope')->toString(),
        ));

        return new JsonResponse;
    }
}
