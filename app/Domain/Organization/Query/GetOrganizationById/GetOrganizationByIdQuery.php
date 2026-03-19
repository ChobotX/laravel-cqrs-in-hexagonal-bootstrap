<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\GetOrganizationById;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Organization\Organization;

/** @implements Query<Organization> */
#[RequiresPermission('organizations.management.read')]
final readonly class GetOrganizationByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
