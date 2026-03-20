<?php

declare(strict_types=1);

namespace App\Infrastructure\Organization;

use App\Contract\Organization\TeamMembershipChecker;
use App\Domain\Organization\TeamMemberRepository;

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
    public function memberTeamIds(string $userId, string $organizationId): array
    {
        return $this->teamMemberRepository->memberTeamIds($userId, $organizationId);
    }

    /** @return list<string> */
    public function visibleUserIds(string $userId, string $organizationId): array
    {
        $teamIds = $this->teamMemberRepository->memberTeamIds($userId, $organizationId);

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
