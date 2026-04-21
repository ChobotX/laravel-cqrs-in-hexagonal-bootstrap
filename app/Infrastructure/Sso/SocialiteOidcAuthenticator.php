<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\Service\SsoAuthenticator;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;
use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Throwable;

use function bin2hex;
use function http_build_query;
use function is_string;
use function random_bytes;

/**
 * Generic OpenID Connect adapter (authorization-code flow).
 *
 * Reads IdP endpoints from `discovery_url` on every call so rotated keys are
 * picked up without admin intervention. The ID token signature is verified by
 * an injected closure (default: `DefaultOidcIdTokenVerifier` — RS256 via JWKS);
 * claim semantics (`aud`, `iss`, `exp`, `nonce`) are enforced by `OidcClaimVerifier`.
 */
final readonly class SocialiteOidcAuthenticator implements SsoAuthenticator
{
    private const int STATE_BYTES = 16;

    private const int NONCE_BYTES = 16;

    /** @var Closure(string, string): array<string, scalar|null> */
    private Closure $idTokenVerifier;

    /**
     * @param  null|Closure(string $idToken, string $jwksUri): array<string, scalar|null>  $idTokenVerifier
     */
    public function __construct(
        private HttpFactory $httpFactory,
        private OidcDiscoveryClient $oidcDiscoveryClient,
        ?Closure $idTokenVerifier = null,
        private OidcClaimVerifier $oidcClaimVerifier = new OidcClaimVerifier,
    ) {
        $this->idTokenVerifier = $idTokenVerifier ?? Closure::fromCallable(new DefaultOidcIdTokenVerifier($httpFactory));
    }

    public function initiate(SsoConfiguration $ssoConfiguration): RedirectInstruction
    {
        $endpoints = $this->oidcDiscoveryClient->fetch($this->stringConfig($ssoConfiguration, 'discovery_url'));
        $state = bin2hex(random_bytes(self::STATE_BYTES));
        $nonce = bin2hex(random_bytes(self::NONCE_BYTES));

        $params = [
            'response_type' => 'code',
            'client_id' => $this->stringConfig($ssoConfiguration, 'client_id'),
            'redirect_uri' => $this->stringConfig($ssoConfiguration, 'redirect_uri'),
            'scope' => $this->stringConfigOrDefault($ssoConfiguration, 'scopes', 'openid email profile'),
            'state' => $state,
            'nonce' => $nonce,
        ];

        return new RedirectInstruction(
            url: $endpoints['authorization_endpoint'].'?'.http_build_query($params),
            stateToStore: $state,
            nonceToStore: $nonce,
        );
    }

    public function complete(SsoConfiguration $ssoConfiguration, array $callbackPayload, ?string $expectedNonce = null): SsoIdentity
    {
        $code = $callbackPayload['code'] ?? null;

        if (! is_string($code) || $code === '') {
            throw new SsoLoginRejectedException('missing_code');
        }

        $endpoints = $this->oidcDiscoveryClient->fetch($this->stringConfig($ssoConfiguration, 'discovery_url'));
        $jwksUri = $endpoints['jwks_uri'] ?? '';

        if ($jwksUri === '') {
            throw new SsoConfigurationInvalidException('Discovery document is missing jwks_uri.');
        }

        $tokens = $this->exchangeCode($ssoConfiguration, $endpoints['token_endpoint'], $code);
        $idToken = $tokens['id_token'] ?? null;

        if (! is_string($idToken)) {
            throw new SsoLoginRejectedException('missing_id_token');
        }

        $claims = ($this->idTokenVerifier)($idToken, $jwksUri);

        $this->oidcClaimVerifier->verify(
            claims: $claims,
            endpoints: $endpoints,
            expectedClientId: $this->stringConfig($ssoConfiguration, 'client_id'),
            expectedNonce: $expectedNonce,
        );

        return $this->buildIdentity($claims);
    }

    public function probe(SsoConfiguration $ssoConfiguration): SsoConnectionTestResult
    {
        try {
            $endpoints = $this->oidcDiscoveryClient->fetch($this->stringConfig($ssoConfiguration, 'discovery_url'));
        } catch (Throwable $throwable) {
            // @silent: probe converts any discovery error into a failure result for the admin UI.
            return new SsoConnectionTestResult(false, $throwable->getMessage());
        }

        $warnings = [];

        foreach (['authorization_endpoint', 'token_endpoint', 'issuer', 'jwks_uri'] as $required) {
            if (($endpoints[$required] ?? '') === '') {
                $warnings[] = 'Missing '.$required.' in discovery document.';
            }
        }

        $success = $warnings === [];

        return new SsoConnectionTestResult(
            success: $success,
            summary: $success ? 'Discovery document fetched successfully.' : 'Discovery document is missing required endpoints.',
            warnings: $warnings,
        );
    }

    /** @param array<string, scalar|null> $claims */
    private function buildIdentity(array $claims): SsoIdentity
    {
        $subject = isset($claims['sub']) && is_string($claims['sub']) ? $claims['sub'] : null;
        $email = isset($claims['email']) && is_string($claims['email']) ? $claims['email'] : null;

        if ($subject === null || $email === null) {
            throw new SsoLoginRejectedException('missing_required_claims');
        }

        $name = isset($claims['name']) && is_string($claims['name']) ? $claims['name'] : null;

        return new SsoIdentity(subject: $subject, email: $email, name: $name);
    }

    /** @return array<string, scalar|null> */
    private function exchangeCode(SsoConfiguration $ssoConfiguration, string $tokenEndpoint, string $code): array
    {
        try {
            $response = $this->httpFactory->asForm()->post($tokenEndpoint, [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => $this->stringConfig($ssoConfiguration, 'client_id'),
                'client_secret' => $this->stringConfig($ssoConfiguration, 'client_secret'),
                'redirect_uri' => $this->stringConfig($ssoConfiguration, 'redirect_uri'),
            ]);
        } catch (ConnectionException $connectionException) {
            throw new SsoLoginRejectedException('token_endpoint_unreachable: '.$connectionException->getMessage());
        }

        if (! $response->successful()) {
            throw new SsoLoginRejectedException('token_exchange_failed');
        }

        /** @var array<string, scalar|null> $tokens */
        $tokens = (array) $response->json();

        return $tokens;
    }

    private function stringConfig(SsoConfiguration $ssoConfiguration, string $key): string
    {
        $value = $ssoConfiguration->config[$key] ?? null;

        return is_string($value) ? $value : '';
    }

    private function stringConfigOrDefault(SsoConfiguration $ssoConfiguration, string $key, string $default): string
    {
        $value = $this->stringConfig($ssoConfiguration, $key);

        return $value !== '' ? $value : $default;
    }
}
