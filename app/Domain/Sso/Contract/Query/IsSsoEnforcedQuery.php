<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * True when any enabled SsoConfiguration has the `enforce` flag set; the
 * presentation login controller uses this to block password authentication.
 *
 * @implements Query<bool>
 */
#[SkipPermissionCheck('Pre-auth login decision; no PII exposed.')]
final readonly class IsSsoEnforcedQuery implements Query {}
