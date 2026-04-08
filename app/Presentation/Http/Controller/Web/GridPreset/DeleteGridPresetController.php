<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\GridPreset;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Domain\GridPreset\Contract\Command\DeleteGridPresetCommand;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck(reason: 'Users manage only their own grid presets')]
final readonly class DeleteGridPresetController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(string $presetId): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteGridPresetCommand(
            id: $presetId,
            userId: $this->authenticatedUser->id() ?? '',
        ));

        return new JsonResponse(['status' => 'ok']);
    }
}
