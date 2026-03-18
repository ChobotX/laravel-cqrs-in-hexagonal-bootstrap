<?php

declare(strict_types=1);

namespace App\Domain\User\Query\GetUserById;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\User;

/** @implements Query<User> */
#[RequiresPermission('users.list.read')]
final readonly class GetUserByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
