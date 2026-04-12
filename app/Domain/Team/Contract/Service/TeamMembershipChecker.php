<?php

declare(strict_types=1);

namespace App\Domain\Team\Contract\Service;

/**
 * Domain service contract for team membership in the Team bounded context.
 */
interface TeamMembershipChecker
{
    /** Evaluates the rule without mutating domain state. */
    public function isTeamMember(string $userId, string $teamId): bool;

    /** @return list<string> */
    public function memberTeamIds(string $userId): array;

    /** @return list<string> User IDs visible under team scope (members of user's teams + descendants) */
    public function visibleUserIds(string $userId): array;
}
