<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Throwable;

use function get_object_vars;

/**
 * Fetches the IdP's JWKS, verifies an OIDC ID token's RS256 signature, and
 * returns the decoded claims.
 */
final readonly class DefaultOidcIdTokenVerifier
{
    public function __construct(
        private HttpFactory $httpFactory,
    ) {}

    /** @return array<string, scalar|null> */
    public function __invoke(string $idToken, string $jwksUri): array
    {
        $document = $this->fetchJwks($jwksUri);

        try {
            $keys = JWK::parseKeySet($document, 'RS256');
        } catch (Throwable $throwable) {
            throw new SsoLoginRejectedException('jwks_invalid: '.$throwable->getMessage());
        }

        try {
            $payload = JWT::decode($idToken, $keys);
        } catch (Throwable $throwable) {
            throw new SsoLoginRejectedException('id_token_signature_invalid: '.$throwable->getMessage());
        }

        /** @var array<string, scalar|null> $claims */
        $claims = get_object_vars($payload);

        return $claims;
    }

    /** @return array<string, mixed> */
    private function fetchJwks(string $jwksUri): array
    {
        try {
            $response = $this->httpFactory->get($jwksUri);
        } catch (ConnectionException $connectionException) {
            throw new SsoLoginRejectedException('jwks_unreachable: '.$connectionException->getMessage());
        }

        if (! $response->successful()) {
            throw new SsoLoginRejectedException('jwks_fetch_failed');
        }

        /** @var array<string, mixed> $document */
        $document = (array) $response->json();

        return $document;
    }
}
