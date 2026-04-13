<?php

declare(strict_types=1);

namespace App\Domain\Notification\Service;

use App\Domain\Notification\Contract\Service\RecipientResolver;
use App\Domain\Team\Contract\Repository\TeamMemberRepository;

final readonly class DefaultRecipientResolver implements RecipientResolver
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
    ) {}

    /** @return list<string> */
    public function resolveTeamMembers(string $teamId): array
    {
        return $this->teamMemberRepository->memberUserIdsForTeam($teamId);
    }

    /** @return list<string> */
    public function resolveTeamWithSubteamMembers(string $teamId): array
    {
        return $this->teamMemberRepository->memberUserIdsForTeamSubtree($teamId);
    }
}
