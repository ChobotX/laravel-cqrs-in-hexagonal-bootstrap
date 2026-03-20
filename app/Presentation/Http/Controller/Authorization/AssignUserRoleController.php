<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Contract\Organization\OrganizationContext;
use App\Domain\Authorization\Command\AssignRoleToUser\AssignRoleToUserCommand;
use App\Presentation\Http\Request\Authorization\AssignRoleRequest;
use Illuminate\Http\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class AssignUserRoleController
{
    public function __construct(
        private CommandBus $commandBus,
        private OrganizationContext $organizationContext,
    ) {}

    public function __invoke(AssignRoleRequest $assignRoleRequest, string $userId): Response
    {
        $organizationId = $this->organizationContext->currentOrganizationId();

        $this->commandBus->dispatch(new AssignRoleToUserCommand(
            userId: $userId,
            roleId: $assignRoleRequest->string('role_id')->toString(),
            organizationId: $organizationId,
        ));

        return new Response(status: 201);
    }
}
