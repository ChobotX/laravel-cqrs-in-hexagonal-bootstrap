<?php

declare(strict_types=1);

namespace App\Domain\Notification\Service;

use App\Contract\Bus\QueryBus;
use App\Domain\Notification\Contract\Service\RecipientResolver;
use App\Domain\Team\Contract\Query\ListTeamMemberUserIdsQuery;
use App\Domain\Team\Contract\Query\ListTeamSubtreeMemberUserIdsQuery;

final readonly class DefaultRecipientResolver implements RecipientResolver
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    /** @return list<string> */
    public function resolveTeamMembers(string $teamId): array
    {
        /** @var list<string> $ids */
        $ids = $this->queryBus->dispatch(new ListTeamMemberUserIdsQuery(teamId: $teamId));

        return $ids;
    }

    /** @return list<string> */
    public function resolveTeamWithSubteamMembers(string $teamId): array
    {
        /** @var list<string> $ids */
        $ids = $this->queryBus->dispatch(new ListTeamSubtreeMemberUserIdsQuery(teamId: $teamId));

        return $ids;
    }
}
