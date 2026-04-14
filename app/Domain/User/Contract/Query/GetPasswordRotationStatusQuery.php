<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Query;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\User\Contract\ValueObject\PasswordRotationUiStatus;

/**
 * @implements Query<PasswordRotationUiStatus>
 */
#[SkipPermissionCheck(reason: 'Authenticated user checks own password rotation window')]
final readonly class GetPasswordRotationStatusQuery implements Query {}
