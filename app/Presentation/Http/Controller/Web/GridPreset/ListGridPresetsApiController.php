<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controller\Web\GridPreset;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Bus\QueryBus;
use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Query\ListGridPresetsQuery;
use App\Domain\User\Contract\Service\AuthenticatedUser;
use App\Presentation\Http\Request\Web\GridPreset\ListGridPresetsRequest;
use Illuminate\Http\JsonResponse;

#[SkipPermissionCheck(reason: 'Grid presets are filtered by visibility — personal, team membership, or global')]
final readonly class ListGridPresetsApiController
{
    public function __construct(
        private QueryBus $queryBus,
        private AuthenticatedUser $authenticatedUser,
    ) {}

    public function __invoke(ListGridPresetsRequest $listGridPresetsRequest): JsonResponse
    {
        $userId = $this->authenticatedUser->id() ?? '';
        $gridName = $listGridPresetsRequest->string('grid_name')->toString();

        $presets = $this->queryBus->dispatch(new ListGridPresetsQuery($userId, $gridName));

        return new JsonResponse([
            'data' => array_map(fn (GridPreset $gridPreset): array => [
                'id' => $gridPreset->id->value,
                'name' => $gridPreset->name,
                'filters' => $gridPreset->filters,
                'sorting' => $gridPreset->sorting,
                'search' => $gridPreset->search,
                'is_default' => $gridPreset->isDefault,
                'position' => $gridPreset->position,
                'scope' => $gridPreset->scope->value,
                'team_id' => $gridPreset->teamId,
                'is_owner' => $gridPreset->userId === $userId,
            ], $presets),
        ]);
    }
}
