<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Pagination\Pagination;
use App\Application\Sorting\Sorting;
use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\Contract\ValueObject\UsersGridResult;

/**
 * Composed read-model query for the users grid. Handler dispatches ListUsersQuery,
 * fans out to role/team/label lookups (gated by per-actor permissions), derives
 * super-admin status, and returns a pre-projected result.
 *
 * @implements Query<UsersGridResult>
 */
#[RequiresPermission('users.list.read')]
final readonly class ListUsersGridQuery implements Query
{
    public function __construct(
        public Pagination $pagination,
        public ?Sorting $sorting,
        public string $search,
        public string $actingUserId,
    ) {}
}
