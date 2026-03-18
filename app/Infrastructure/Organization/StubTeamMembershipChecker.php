<?php

declare(strict_types=1);

namespace App\Infrastructure\Organization;

use App\Contract\Organization\TeamMembershipChecker;

final readonly class StubTeamMembershipChecker implements TeamMembershipChecker
{
    public function isTeamMember(string $userId, string $teamId): bool
    {
        return false;
    }

    /** @return list<string> */
    public function memberTeamIds(string $userId, string $organizationId): array
    {
        return [];
    }
}
