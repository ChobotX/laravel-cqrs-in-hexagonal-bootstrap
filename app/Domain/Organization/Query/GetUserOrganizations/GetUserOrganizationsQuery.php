<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\GetUserOrganizations;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Organization\Organization;

/** @implements Query<list<Organization>> */
#[SkipPermissionCheck(reason: 'Used for context resolution and org switcher — every authenticated user needs their own org list')]
final readonly class GetUserOrganizationsQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
