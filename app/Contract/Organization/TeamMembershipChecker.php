<?php

declare(strict_types=1);

namespace App\Contract\Organization;

interface TeamMembershipChecker
{
    public function isTeamMember(string $userId, string $teamId): bool;

    /** @return list<string> */
    public function memberTeamIds(string $userId, string $organizationId): array;
}
