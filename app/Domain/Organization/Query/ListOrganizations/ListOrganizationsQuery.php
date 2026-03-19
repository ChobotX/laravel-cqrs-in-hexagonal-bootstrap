<?php

declare(strict_types=1);

namespace App\Domain\Organization\Query\ListOrganizations;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Organization\Organization;

/** @implements Query<list<Organization>> */
#[RequiresPermission('organizations.management.read')]
final readonly class ListOrganizationsQuery implements Query {}
