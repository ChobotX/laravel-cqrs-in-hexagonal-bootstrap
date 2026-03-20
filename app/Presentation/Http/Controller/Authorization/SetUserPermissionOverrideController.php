<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Command\SetPermissionOverride\SetPermissionOverrideCommand;
use App\Presentation\Http\Request\Authorization\SetPermissionOverrideRequest;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class SetUserPermissionOverrideController
{
    public function __construct(
        private CommandBus $commandBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(SetPermissionOverrideRequest $setPermissionOverrideRequest, string $userId): JsonResponse
    {
        $organizationId = $this->organizationContext->currentOrganizationId();

        $this->commandBus->dispatch(new SetPermissionOverrideCommand(
            userId: $userId,
            organizationId: $organizationId,
            permission: $setPermissionOverrideRequest->string('permission')->toString(),
            type: $setPermissionOverrideRequest->string('type')->toString(),
            scope: $setPermissionOverrideRequest->string('scope')->toString(),
        ));

        return new JsonResponse;
    }
}
