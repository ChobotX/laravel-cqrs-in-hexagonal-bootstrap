<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Handler\Query;

use App\Contract\Auth\TeamMembershipChecker;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Query\GetDefaultGridPresetQuery;
use App\Domain\GridPreset\Contract\Repository\GridPresetRepository;

/** @implements QueryHandler<GetDefaultGridPresetQuery, ?GridPreset> */
final readonly class GetDefaultGridPresetHandler implements QueryHandler
{
    public function __construct(
        private GridPresetRepository $gridPresetRepository,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    public function handle(Query $query): ?GridPreset
    {
        $teamIds = $this->teamMembershipChecker->memberTeamIds($query->userId);
        $presets = $this->gridPresetRepository->findVisibleByGrid($query->userId, $query->gridName, $teamIds);

        foreach ($presets as $preset) {
            if ($preset->isDefault) {
                return $preset;
            }
        }

        return null;
    }
}
