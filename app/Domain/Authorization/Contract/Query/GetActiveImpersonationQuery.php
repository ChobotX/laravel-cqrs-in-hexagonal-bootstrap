<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;

/** @implements Query<array{impersonator_id: string, impersonated_user_id: string}|null> */
#[SkipPermissionCheck(reason: 'Used internally for impersonation state checks')]
final readonly class GetActiveImpersonationQuery implements Query
{
    public function __construct(
        public string $impersonatorId,
    ) {}
}
