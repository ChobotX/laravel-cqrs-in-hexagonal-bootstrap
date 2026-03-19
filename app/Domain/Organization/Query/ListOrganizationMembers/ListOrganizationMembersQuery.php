<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\ListOrganizationMembers;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Organization\OrganizationMember;

/** @implements Query<list<OrganizationMember>> */
#[RequiresPermission('organizations.members.read')]
final readonly class ListOrganizationMembersQuery implements Query
{
    public function __construct(
        public string $organizationId,
    ) {}
}
