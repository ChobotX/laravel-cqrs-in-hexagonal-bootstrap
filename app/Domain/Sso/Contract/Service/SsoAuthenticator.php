<?php

declare(strict_types=1);

namespace App\Domain\Sso\Contract\Service;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;

/**
 * Adapter for one IdP protocol family. Concrete implementations live in `App\Infrastructure\Sso`.
 *
 * Implementations are selected by `SsoAuthenticatorRegistry` based on `ProviderType`.
 */
interface SsoAuthenticator
{
    /** Build the redirect/POST that starts the IdP flow for `$configuration`. */
    public function initiate(SsoConfiguration $ssoConfiguration): RedirectInstruction;

    /**
     * Validate the IdP callback and return a verified SsoIdentity.
     *
     * Receives the raw callback payload (query string for OAuth/OIDC, SAMLResponse for SAML).
     * `$expectedNonce` is the OIDC nonce previously stored with the `state`; the adapter
     * verifies the ID token's `nonce` claim equals it. Null for protocols without nonces.
     *
     * @param  array<string, scalar|array<int|string, mixed>|null>  $callbackPayload
     */
    public function complete(SsoConfiguration $ssoConfiguration, array $callbackPayload, ?string $expectedNonce = null): SsoIdentity;

    /** Non-interactive probe (e.g. fetch OIDC discovery or SAML metadata). */
    public function probe(SsoConfiguration $ssoConfiguration): SsoConnectionTestResult;
}
