<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Authorization;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\Authorization\Command\RemovePermissionOverride\RemovePermissionOverrideCommand;
use Illuminate\Http\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class RemoveUserPermissionOverrideController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $userId, string $permission): Response
    {
        $this->commandBus->dispatch(new RemovePermissionOverrideCommand(
            userId: $userId,
            permission: $permission,
        ));

        return new Response(status: 204);
    }
}
