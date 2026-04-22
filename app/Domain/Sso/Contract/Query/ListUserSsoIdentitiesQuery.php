<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Query;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\Sso\Contract\Entity\UserSsoIdentity;

/**
 * Lists SSO identity links for a user.
 *
 * @implements Query<list<UserSsoIdentity>>
 */
#[RequiresPermission('sso.management.read')]
final readonly class ListUserSsoIdentitiesQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
