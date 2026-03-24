<?php

declare(strict_types=1);

namespace App\Infrastructure\Team;

use App\Contract\Team\TeamMembershipChecker;
use App\Domain\Team\TeamMemberRepository;

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
        $teamIds = $this->teamMemberRepository->memberTeamIds($userId);

        if ($teamIds === []) {
            return [$userId];
        }

        $userIds = [$userId];

        foreach ($teamIds as $teamId) {
            foreach ($this->teamMemberRepository->listMembers($teamId) as $member) {
                $userIds[] = $member->userId;
            }
        }

        return array_values(array_unique($userIds));
    }
}
