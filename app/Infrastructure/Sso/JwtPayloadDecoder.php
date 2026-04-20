<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;

use function base64_decode;
use function explode;
use function is_array;
use function json_decode;
use function str_repeat;
use function strlen;
use function strtr;
use function substr_count;

/**
 * Decodes the payload segment of a JWT without verifying the signature.
 *
 * Authenticity for OIDC IdP tokens is established by the TLS-protected token
 * endpoint exchange that produced the JWT (the IdP signs over a request bound to
 * our client_secret). Claim-level checks (`aud`, `iss`, `exp`) are performed by the
 * caller after decoding.
 */
final readonly class JwtPayloadDecoder
{
    private const int JWT_DOT_COUNT = 2;

    private const int JWT_SEGMENTS = 3;

    private const int BASE64_PAD_BASE = 4;

    /** @return array<string, scalar|null> */
    public function decode(string $idToken): array
    {
        if (substr_count($idToken, '.') !== self::JWT_DOT_COUNT) {
            throw new SsoLoginRejectedException('malformed_id_token');
        }

        $segments = explode('.', $idToken, self::JWT_SEGMENTS);
        $payload = $segments[1];
        $padded = strtr($payload, '-_', '+/');
        $padded .= str_repeat('=', (self::BASE64_PAD_BASE - (strlen($padded) % self::BASE64_PAD_BASE)) % self::BASE64_PAD_BASE);

        $decoded = base64_decode($padded, strict: true);

        if ($decoded === false) {
            throw new SsoLoginRejectedException('malformed_id_token');
        }

        /** @var array<string, scalar|null>|null $claims */
        $claims = json_decode($decoded, associative: true);

        if (! is_array($claims)) {
            throw new SsoLoginRejectedException('malformed_id_token');
        }

        return $claims;
    }
}
