<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * @implements Query<list<string>|null>
 */
#[SkipPermissionCheck(reason: 'Authenticated user downloads own pending backup codes')]
final readonly class GetPendingTotpBackupCodesPayloadQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
