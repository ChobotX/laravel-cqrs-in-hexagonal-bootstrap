<?php

declare(strict_types=1);

namespace App\Domain\User\Query\SearchUsers;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\User;

/** @implements Query<list<User>> */
#[RequiresPermission('users.list.read')]
final readonly class SearchUsersQuery implements Query
{
    /** @param  list<string>  $excludeUserIds */
    public function __construct(
        public string $term,
        public array $excludeUserIds,
        public int $limit = 10,
    ) {}
}
