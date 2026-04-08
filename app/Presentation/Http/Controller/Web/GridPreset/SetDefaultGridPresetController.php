<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\GridPreset;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\GridPreset\Contract\Command\SetDefaultGridPresetCommand;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\GridPreset\SetDefaultGridPresetRequest;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck(reason: 'Users manage only their own grid presets')]
final readonly class SetDefaultGridPresetController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(SetDefaultGridPresetRequest $setDefaultGridPresetRequest, string $presetId): JsonResponse
    {
        $this->commandBus->dispatch(new SetDefaultGridPresetCommand(
            id: $presetId,
            userId: $this->authenticatedUser->id() ?? '',
            gridName: $setDefaultGridPresetRequest->string('grid_name')->toString(),
        ));

        return new JsonResponse(['status' => 'ok']);
    }
}
