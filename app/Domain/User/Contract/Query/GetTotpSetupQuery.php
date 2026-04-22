<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\User\Contract\ValueObject\TotpSetup;

/**
 * @implements Query<TotpSetup>
 */
#[SkipPermissionCheck(reason: 'Authenticated users check own TOTP setup')]
final readonly class GetTotpSetupQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {}
}
