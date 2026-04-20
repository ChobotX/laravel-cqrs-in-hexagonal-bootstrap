<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\ValueObject;

/**
 * Identity returned by an SsoAuthenticator after a successful IdP callback.
 *
 * Carries only the data the orchestrator needs to find or provision a user.
 * Subject is the IdP's stable user identifier (NOT email).
 */
final readonly class SsoIdentity
{
    public function __construct(
        /** Stable subject identifier issued by the IdP (e.g. OIDC `sub`, SAML NameID). */
        public string $subject,
        /** Email address asserted by the IdP. */
        public string $email,
        /** Display name when the IdP supplies one. */
        public ?string $name = null,
    ) {}
}
