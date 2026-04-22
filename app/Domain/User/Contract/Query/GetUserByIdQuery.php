<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\User\Contract\Entity\User;

/**
 * Query for get user by id in the User bounded context; dispatched through the query bus.
 *
 * @implements Query<User>
 */
#[RequiresPermission('users.list.read')]
final readonly class GetUserByIdQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
    ) {}
}
