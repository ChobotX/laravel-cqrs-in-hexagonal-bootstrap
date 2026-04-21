<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;

use function is_numeric;
use function is_string;
use function time;

/**
 * Enforces OIDC ID-token claim semantics: `aud` equals the configured client_id,
 * `iss` matches the discovery-document issuer, `exp` is in the future, and the
 * optional `nonce` equals what was stored before redirect.
 */
final readonly class OidcClaimVerifier
{
    /**
     * @param  array<string, scalar|null>  $claims
     * @param  array<string, string>  $endpoints
     */
    public function verify(array $claims, array $endpoints, string $expectedClientId, ?string $expectedNonce): void
    {
        $this->verifyAudience($claims, $expectedClientId);
        $this->verifyIssuer($claims, $endpoints);
        $this->verifyExpiration($claims);
        $this->verifyNonce($claims, $expectedNonce);
    }

    /** @param array<string, scalar|null> $claims */
    private function verifyAudience(array $claims, string $expectedClientId): void
    {
        $audience = isset($claims['aud']) && is_string($claims['aud']) ? $claims['aud'] : null;

        if ($audience !== $expectedClientId) {
            throw new SsoLoginRejectedException('aud_mismatch');
        }
    }

    /**
     * @param  array<string, scalar|null>  $claims
     * @param  array<string, string>  $endpoints
     */
    private function verifyIssuer(array $claims, array $endpoints): void
    {
        $expected = $endpoints['issuer'] ?? '';

        if ($expected === '') {
            throw new SsoConfigurationInvalidException('Discovery document is missing issuer.');
        }

        $issuer = isset($claims['iss']) && is_string($claims['iss']) ? $claims['iss'] : null;

        if ($issuer !== $expected) {
            throw new SsoLoginRejectedException('iss_mismatch');
        }
    }

    /** @param array<string, scalar|null> $claims */
    private function verifyExpiration(array $claims): void
    {
        $expiresAt = isset($claims['exp']) && is_numeric($claims['exp']) ? (int) $claims['exp'] : null;

        if ($expiresAt !== null && $expiresAt < time()) {
            throw new SsoLoginRejectedException('id_token_expired');
        }
    }

    /** @param array<string, scalar|null> $claims */
    private function verifyNonce(array $claims, ?string $expectedNonce): void
    {
        if ($expectedNonce === null) {
            return;
        }

        $nonce = isset($claims['nonce']) && is_string($claims['nonce']) ? $claims['nonce'] : null;

        if ($nonce !== $expectedNonce) {
            throw new SsoLoginRejectedException('nonce_mismatch');
        }
    }
}
