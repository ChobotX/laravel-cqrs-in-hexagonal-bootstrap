<?php

declare(strict_types=1);

namespace App\Domain\User\Query\ListUsers;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\User;

/** @implements Query<list<User>> */
#[RequiresPermission('users.list.read')]
final readonly class ListUsersQuery implements Query {}
