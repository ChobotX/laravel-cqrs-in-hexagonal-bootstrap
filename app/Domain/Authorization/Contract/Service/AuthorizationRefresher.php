<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Service;

/**
 * Domain service contract for authorization in the Authorization bounded context.
 */
interface AuthorizationRefresher
{
    /** Contract operation `refreshForUser`; see infrastructure for behavior. */
    public function refreshForUser(string $userId): void;
}
