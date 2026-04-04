<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\User\Contract\User;

/** @implements Query<User> */
#[SkipPermissionCheck(reason: 'Users can always view their own profile')]
final readonly class GetOwnProfileQuery implements Query
{
    public function __construct(
        public string $id,
    ) {}
}
