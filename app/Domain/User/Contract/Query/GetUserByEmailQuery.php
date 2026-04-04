<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\User\Contract\Entity\User;

/** @implements Query<?User> */
#[SkipPermissionCheck(reason: 'Used internally for login/authentication')]
final readonly class GetUserByEmailQuery implements Query
{
    public function __construct(
        public string $email,
    ) {}
}
