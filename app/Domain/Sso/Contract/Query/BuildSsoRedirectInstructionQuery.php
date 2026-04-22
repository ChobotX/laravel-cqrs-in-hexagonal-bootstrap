<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Query;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Query\Query;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;

/**
 * Resolves a configured SSO provider by slug and returns the IdP redirect that
 * starts the login flow. Pre-auth — the IdP enforces the next step.
 *
 * @implements Query<RedirectInstruction>
 */
#[SkipPermissionCheck('Pre-auth SSO initiation; the IdP guards the next hop.')]
final readonly class BuildSsoRedirectInstructionQuery implements Query
{
    public function __construct(
        public string $slug,
    ) {}
}
