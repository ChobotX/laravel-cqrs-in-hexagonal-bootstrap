<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Authorization\Contract\Command\AssignRoleToUserCommand;
use App\Presentation\Http\Request\Authorization\AssignRoleRequest;
use Illuminate\Http\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class AssignUserRoleController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(AssignRoleRequest $assignRoleRequest, string $userId): Response
    {
        $this->commandBus->dispatch(new AssignRoleToUserCommand(
            userId: $userId,
            roleId: $assignRoleRequest->string('role_id')->toString(),
        ));

        return new Response(status: HttpStatus::CREATED);
    }
}
