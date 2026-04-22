<?php

declare(strict_types=1);

namespace App\Infrastructure\Team;

use App\Contract\Auth\TeamMembershipChecker;
use App\Domain\Team\Contract\Repository\TeamMemberRepository;

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
    public function directMemberTeamIds(string $userId): array
    {
        return $this->teamMemberRepository->directMemberTeamIds($userId);
    }

    /** @return list<string> */
    public function visibleUserIds(string $userId): array
    {
        return $this->teamMemberRepository->visibleUserIds($userId);
    }

    /** @return list<string> */
    public function directVisibleUserIds(string $userId): array
    {
        return $this->teamMemberRepository->directVisibleUserIds($userId);
    }
}
