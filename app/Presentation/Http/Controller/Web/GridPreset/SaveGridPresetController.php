<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\GridPreset;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\CommandBus;
use App\Contract\IdGenerator;
use App\Domain\GridPreset\Contract\Command\SaveGridPresetCommand;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\GridPreset\SaveGridPresetRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[SkipPermissionCheck(reason: 'Scope permission is enforced in SaveGridPresetHandler')]
final readonly class SaveGridPresetController
{
    public function __construct(
        private CommandBus $commandBus,
        private AuthenticatedUser $authenticatedUser,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(SaveGridPresetRequest $saveGridPresetRequest): JsonResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';
        $presetId = $saveGridPresetRequest->string('preset_id')->toString();

        $this->commandBus->dispatch(new SaveGridPresetCommand(
            id: $presetId !== '' ? $presetId : $this->idGenerator->generate(),
            userId: $userId,
            gridName: $saveGridPresetRequest->string('grid_name')->toString(),
            name: $saveGridPresetRequest->string('name')->toString(),
            filters: $saveGridPresetRequest->string('filters')->toString(),
            sorting: $saveGridPresetRequest->string('sorting')->toString(),
            search: $saveGridPresetRequest->string('search')->toString(),
            isDefault: $saveGridPresetRequest->boolean('is_default'),
            position: 0,
            scope: $saveGridPresetRequest->string('scope', 'personal')->toString(),
            teamId: $saveGridPresetRequest->filled('team_id') ? $saveGridPresetRequest->string('team_id')->toString() : null,
        ));

        return new JsonResponse(['status' => 'ok'], Response::HTTP_CREATED);
    }
}
