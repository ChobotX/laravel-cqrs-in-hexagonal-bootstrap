<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\Contract\Entity\User;

/**
 * Batch-loads users by a list of IDs. Used by views that need grantee details
 * for a collection of record shares in a single query.
 *
 * @implements Query<list<User>>
 */
#[RequiresPermission('users.list.read')]
final readonly class GetUsersByIdsQuery implements Query
{
    /** @param list<string> $userIds */
    public function __construct(
        public array $userIds,
    ) {}
}
