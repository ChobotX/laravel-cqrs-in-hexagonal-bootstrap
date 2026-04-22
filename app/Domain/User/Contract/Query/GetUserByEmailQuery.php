<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\User\Contract\Entity\User;

/**
 * Query for get user by email in the User bounded context; dispatched through the query bus.
 *
 * @implements Query<?User>
 */
#[SkipPermissionCheck(reason: 'Used internally for login/authentication')]
final readonly class GetUserByEmailQuery implements Query
{
    public function __construct(
        /** Email address used for lookup, delivery, or authentication flows. */
        public string $email,
    ) {}
}
