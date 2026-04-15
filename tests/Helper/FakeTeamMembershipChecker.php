<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Team\Contract\Service\TeamMembershipChecker;

final readonly class FakeTeamMembershipChecker implements TeamMembershipChecker
{
    /**
     * @param  array<string, list<string>>  $userTeams  userId => [teamId, ...]
     */
    public function __construct(
        private array $userTeams = [],
    ) {}

    public function isTeamMember(string $userId, string $teamId): bool
    {
        return in_array($teamId, $this->userTeams[$userId] ?? [], true);
    }

    /** @return list<string> */
    public function memberTeamIds(string $userId): array
    {
        return $this->userTeams[$userId] ?? [];
    }

    /** @return list<string> */
    public function directMemberTeamIds(string $userId): array
    {
        return $this->userTeams[$userId] ?? [];
    }

    /** @return list<string> */
    public function visibleUserIds(string $userId): array
    {
        return [];
    }

    /** @return list<string> */
    public function directVisibleUserIds(string $userId): array
    {
        return [];
    }
}
