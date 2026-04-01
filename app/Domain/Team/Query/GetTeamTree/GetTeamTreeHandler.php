<?php

declare(strict_types=1);

namespace App\Domain\Team\Query\GetTeamTree;

use App\Application\Sorting\SortDirection;
use App\Application\Sorting\Sorting;
use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Team\Team;
use App\Domain\Team\TeamMemberRepository;
use App\Domain\Team\TeamRepository;

/** @implements QueryHandler<GetTeamTreeQuery, list<TeamTreeNode>> */
final readonly class GetTeamTreeHandler implements QueryHandler
{
    public function __construct(
        private TeamRepository $teamRepository,
        private TeamMemberRepository $teamMemberRepository,
    ) {}

    /** @return list<TeamTreeNode> */
    public function handle(Query $query): array
    {
        $visibleIds = $query->accessContext()?->visibleIds;
        $teams = $this->teamRepository->findAll($visibleIds);
        $memberSortings = [new Sorting(Sorting::PERMISSION_SCORE, SortDirection::Desc)];

        return array_map(
            fn (Team $team): TeamTreeNode => new TeamTreeNode(
                team: $team,
                members: $this->teamMemberRepository->listMembers($team->id->value, $memberSortings),
            ),
            $teams,
        );
    }
}
