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
}
