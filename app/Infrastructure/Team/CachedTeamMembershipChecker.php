<?php

declare(strict_types=1);

namespace App\Infrastructure\Team;

use App\Domain\Team\Contract\Service\TeamMembershipChecker;

final class CachedTeamMembershipChecker implements TeamMembershipChecker
{
    /** @var array<string, list<string>> */
    private array $memberTeamIdsCache = [];

    /** @var array<string, list<string>> */
    private array $directMemberTeamIdsCache = [];

    /** @var array<string, list<string>> */
    private array $visibleUserIdsCache = [];

    /** @var array<string, list<string>> */
    private array $directVisibleUserIdsCache = [];

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
    public function directMemberTeamIds(string $userId): array
    {
        return $this->directMemberTeamIdsCache[$userId] ??= $this->teamMembershipChecker->directMemberTeamIds($userId);
    }

    /** @return list<string> */
    public function visibleUserIds(string $userId): array
    {
        return $this->visibleUserIdsCache[$userId] ??= $this->teamMembershipChecker->visibleUserIds($userId);
    }

    /** @return list<string> */
    public function directVisibleUserIds(string $userId): array
    {
        return $this->directVisibleUserIdsCache[$userId] ??= $this->teamMembershipChecker->directVisibleUserIds($userId);
    }
}
