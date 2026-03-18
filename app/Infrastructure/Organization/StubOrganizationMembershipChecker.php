<?php

declare(strict_types=1);

namespace App\Infrastructure\Organization;

use App\Contract\Organization\OrganizationMembershipChecker;

final readonly class StubOrganizationMembershipChecker implements OrganizationMembershipChecker
{
    public function isMember(string $userId, string $organizationId): bool
    {
        return true;
    }

    /** @return list<string> */
    public function memberOrganizationIds(string $userId): array
    {
        return [];
    }
}
