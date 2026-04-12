<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * Query for get active impersonation in the Authorization bounded context; dispatched through the query bus.
 *
 * @implements Query<array{impersonator_id: string, impersonated_user_id: string}|null>
 */
#[SkipPermissionCheck(reason: 'Used internally for impersonation state checks')]
final readonly class GetActiveImpersonationQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $impersonatorId,
    ) {}
}
