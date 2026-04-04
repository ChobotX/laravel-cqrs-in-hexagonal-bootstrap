<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query\CountUsers;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;

/** @implements Query<int> */
#[RequiresPermission('users.list.read')]
final readonly class CountUsersQuery implements Query {}
