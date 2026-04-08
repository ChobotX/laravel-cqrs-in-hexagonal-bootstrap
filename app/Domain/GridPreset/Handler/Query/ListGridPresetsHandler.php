<?php

declare(strict_types=1);

namespace App\Domain\GridPreset\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\GridPreset\Contract\Entity\GridPreset;
use App\Domain\GridPreset\Contract\Query\ListGridPresetsQuery;
use App\Domain\GridPreset\Contract\Repository\GridPresetRepository;
use App\Domain\Team\Contract\Service\TeamMembershipChecker;

/** @implements QueryHandler<ListGridPresetsQuery, list<GridPreset>> */
final readonly class ListGridPresetsHandler implements QueryHandler
{
    public function __construct(
        private GridPresetRepository $gridPresetRepository,
        private TeamMembershipChecker $teamMembershipChecker,
    ) {}

    /** @return list<GridPreset> */
    public function handle(Query $query): array
    {
        $teamIds = $this->teamMembershipChecker->memberTeamIds($query->userId);

        return $this->gridPresetRepository->findVisibleByGrid($query->userId, $query->gridName, $teamIds);
    }
}
