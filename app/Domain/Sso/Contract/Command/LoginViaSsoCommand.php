<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Completes an IdP callback: validates payload, applies JitMode, links/creates user, raises events.
 *
 * Pre-auth — no acting user. Authorization is enforced by the IdP signature/state checks
 * inside the SsoAuthenticator and by JitMode policy in the handler.
 */
#[SkipPermissionCheck('Pre-auth login flow; identity is asserted by the IdP and validated by the SsoAuthenticator.')]
final readonly class LoginViaSsoCommand implements Command
{
    /** @param array<string, scalar|array<int|string, mixed>|null> $callbackPayload */
    public function __construct(
        /** Target SsoConfiguration UUID. */
        public string $configurationId,
        /** Raw callback payload from the IdP (query/body). */
        public array $callbackPayload,
        /** CSRF `state` echoed back by the IdP; compared against the stored handshake. */
        public string $state,
        /** UUID to assign to a newly provisioned user (auto_create JitMode); ignored otherwise. */
        public string $newUserIdIfProvisioned,
        /** UUID to assign to a newly created identity link row. */
        public string $newIdentityId,
    ) {}
}
