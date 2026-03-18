<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Command\RemovePermissionOverride\RemovePermissionOverrideCommand;
use App\Domain\Authorization\Exception\PermissionDeniedException;
use Illuminate\Http\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class RemoveUserPermissionOverrideController
{
    public function __construct(
        private CommandBus $commandBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(string $userId, string $permission): Response
    {
        $organizationId = $this->organizationContext->currentOrganizationId()
            ?? throw new PermissionDeniedException;

        $this->commandBus->dispatch(new RemovePermissionOverrideCommand(
            userId: $userId,
            organizationId: $organizationId,
            permission: $permission,
        ));

        return new Response(status: 204);
    }
}
