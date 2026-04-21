<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\ValueObject;

/**
 * Where the user agent should go to begin the IdP flow.
 *
 * For OAuth/OIDC this is the authorization URL; for SAML POST binding the
 * presentation layer turns this into an HTML form auto-submitted to `url`
 * with `formFields` carrying SAMLRequest + RelayState.
 *
 * `stateToStore` and `nonceToStore` are carried back to the caller so the
 * callback handler can verify the IdP's reply against the same `state` (CSRF)
 * and, for OIDC, the same `nonce` (replay) — stored via `SsoLoginSession`.
 */
final readonly class RedirectInstruction
{
    /** @param array<string, string> $formFields */
    public function __construct(
        /** Absolute URL the browser must navigate to (GET) or POST to. */
        public string $url,
        /** True for SAML POST binding; false for HTTP redirect. */
        public bool $usesPostBinding = false,
        /** Form fields to POST when `usesPostBinding` is true. */
        public array $formFields = [],
        /** CSRF `state` to store for this flow; null when the protocol does not use it. */
        public ?string $stateToStore = null,
        /** Replay `nonce` to store for this flow; null when the protocol does not use it. */
        public ?string $nonceToStore = null,
    ) {}
}
