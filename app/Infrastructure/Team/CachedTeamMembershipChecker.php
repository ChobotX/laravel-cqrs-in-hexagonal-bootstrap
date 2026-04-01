<?php

declare(strict_types=1);

namespace App\Infrastructure\Team;

use App\Domain\Team\Contract\TeamMembershipChecker;

final class CachedTeamMembershipChecker implements TeamMembershipChecker
{
    /** @var array<string, list<string>> */
    private array $memberTeamIdsCache = [];

    /** @var array<string, list<string>> */
    private array $visibleUserIdsCache = [];

    public function __construct(
        private readonly TeamMembershipChecker $teamMembershipChecker,
    ) {}

    public function isTeamMember(string $userId, string $teamId): bool
    {
        return $this->teamMembershipChecker->isTeamMember($userId, $teamId);
    }

    /** @return list<string> */
    public function memberTeamIds(string $userId): array
    {
        return $this->memberTeamIdsCache[$userId] ??= $this->teamMembershipChecker->memberTeamIds($userId);
    }

    /** @return list<string> */
    public function visibleUserIds(string $userId): array
    {
        return $this->visibleUserIdsCache[$userId] ??= $this->teamMembershipChecker->visibleUserIds($userId);
    }
}
