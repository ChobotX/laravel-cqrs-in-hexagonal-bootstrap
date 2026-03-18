<?php

declare(strict_types=1);

namespace App\Contract\Organization;

interface OrganizationMembershipChecker
{
    public function isMember(string $userId, string $organizationId): bool;

    /** @return list<string> */
    public function memberOrganizationIds(string $userId): array;
}
