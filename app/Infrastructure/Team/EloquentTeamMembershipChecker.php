<?php

declare(strict_types=1);

namespace App\Infrastructure\Team;

use App\Domain\Team\Contract\Repository\TeamMemberRepository;
use App\Domain\Team\Contract\Service\TeamMembershipChecker;

final readonly class EloquentTeamMembershipChecker implements TeamMembershipChecker
{
    public function __construct(
        private TeamMemberRepository $teamMemberRepository,
    ) {}

    public function isTeamMember(string $userId, string $teamId): bool
    {
        return $this->teamMemberRepository->isMember($userId, $teamId);
    }

    /** @return list<string> */
    public function memberTeamIds(string $userId): array
    {
        return $this->teamMemberRepository->memberTeamIds($userId);
    }

    /** @return list<string> */
    public function visibleUserIds(string $userId): array
    {
        return $this->teamMemberRepository->visibleUserIds($userId);
    }
}
