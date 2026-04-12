<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for count roles in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<int>
 */
#[RequiresPermission('users.roles.read')]
final readonly class CountRolesQuery implements Query {}
