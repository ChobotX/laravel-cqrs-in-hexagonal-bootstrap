<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\User\Contract\ValueObject\TwoFactorUiStatus;

/**
 * @implements Query<TwoFactorUiStatus>
 */
#[SkipPermissionCheck(reason: 'Authenticated user checks own two-factor status')]
final readonly class GetTwoFactorStatusQuery implements Query {}
