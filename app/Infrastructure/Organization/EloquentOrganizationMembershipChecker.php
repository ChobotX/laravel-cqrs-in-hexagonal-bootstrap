<?php

declare(strict_types=1);

namespace App\Infrastructure\Organization;

use App\Contract\Organization\OrganizationMembershipChecker;
use App\Infrastructure\Eloquent\Organization\OrganizationMemberModel;

final readonly class EloquentOrganizationMembershipChecker implements OrganizationMembershipChecker
{
    public function isMember(string $userId, string $organizationId): bool
    {
        return OrganizationMemberModel::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->exists();
    }

    /** @return list<string> */
    public function memberOrganizationIds(string $userId): array
    {
        /** @var list<string> $ids */
        $ids = array_values(
            OrganizationMemberModel::where('user_id', $userId)
                ->pluck('organization_id')
                ->all(),
        );

        return $ids;
    }
}
