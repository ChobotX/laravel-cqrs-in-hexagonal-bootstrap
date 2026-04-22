<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Sso\Contract\ValueObject\EnabledSsoProvider;

/**
 * Lists enabled SSO providers for the current tenant; safe to call pre-auth.
 *
 * @implements Query<list<EnabledSsoProvider>>
 */
#[SkipPermissionCheck('Login page consumes this pre-auth; payload contains no secrets.')]
final readonly class GetEnabledSsoProvidersQuery implements Query {}
