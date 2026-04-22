<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Command;

use App\Contract\Attribute\SkipPermissionCheck;
use App\Contract\Command\Command;

/**
 * Persists a UserSsoIdentity row linking a user to an IdP subject.
 *
 * Dispatched internally by the login orchestrator after a successful provisioning.
 */
#[SkipPermissionCheck('Internal step of the SSO login flow; the orchestrator has already verified the IdP-asserted identity.')]
final readonly class LinkSsoIdentityCommand implements Command
{
    public function __construct(
        /** New identity row UUID. */
        public string $id,
        /** User the identity belongs to. */
        public string $userId,
        /** Configuration the subject was issued under. */
        public string $configurationId,
        /** IdP subject identifier. */
        public string $subject,
        /** Email value at the time of linking. */
        public string $emailAtLink,
    ) {}
}
