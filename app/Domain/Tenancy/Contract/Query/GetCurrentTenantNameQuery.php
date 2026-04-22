<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;

/**
 * Returns the display name of the currently resolved tenant, or null when no tenant is resolved
 * (root-domain requests like landing pages or tenant registration).
 *
 * @implements Query<?string>
 */
#[SkipPermissionCheck(reason: 'Reads current tenant name; anyone with a resolved tenant context can access it')]
final readonly class GetCurrentTenantNameQuery implements Query {}
