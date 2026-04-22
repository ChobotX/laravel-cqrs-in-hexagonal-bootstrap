<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Api\V1\Authorization;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\CommandBus;
use App\Contract\Http\HttpStatus;
use App\Domain\Authorization\Contract\Command\DeleteRoleCommand;
use Illuminate\Http\Response;

#[SkipPermissionCheck('Permission enforced by command/query bus')]
final readonly class DeleteRoleController
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(string $roleId): Response
    {
        $this->commandBus->dispatch(new DeleteRoleCommand($roleId));

        return new Response(status: HttpStatus::NO_CONTENT);
    }
}
