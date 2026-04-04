<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query\CountRoles;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/** @implements Query<int> */
#[RequiresPermission('users.roles.read')]
final readonly class CountRolesQuery implements Query {}
