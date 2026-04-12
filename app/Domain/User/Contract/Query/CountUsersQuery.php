<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/**
 * Query for count users in the User bounded context; dispatched through the query bus.
 *
 * @implements Query<int>
 */
#[RequiresPermission('users.list.read')]
final readonly class CountUsersQuery implements Query {}
