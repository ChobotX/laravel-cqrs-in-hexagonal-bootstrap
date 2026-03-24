<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\Authorization\Command\RevokeRoleFromUser\RevokeRoleFromUserCommand;
use Illuminate\Http\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class RevokeUserRoleController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $userId, string $roleId): Response
    {
        $this->commandBus->dispatch(new RevokeRoleFromUserCommand(
            userId: $userId,
            roleId: $roleId,
        ));

        return new Response(status: 204);
    }
}
