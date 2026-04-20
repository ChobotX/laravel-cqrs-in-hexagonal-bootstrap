<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\Service\SsoAuthenticator;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Throwable;

use function bin2hex;
use function http_build_query;
use function is_numeric;
use function is_string;
use function random_bytes;
use function time;

/**
 * Generic OpenID Connect adapter (authorization-code flow).
 *
 * Reads IdP endpoints from `discovery_url` on every call so that rotated keys are
 * picked up without admin intervention. The ID token is accepted only after the
 * token endpoint exchange has succeeded (establishes provenance via client secret
 * over TLS) and the `aud`, `iss`, `exp` claims match expectations.
 */
final readonly class SocialiteOidcAuthenticator implements SsoAuthenticator
{
    private const int STATE_BYTES = 16;

    public function __construct(
        private HttpFactory $httpFactory,
        private OidcDiscoveryClient $oidcDiscoveryClient,
        private JwtPayloadDecoder $jwtPayloadDecoder,
    ) {}

    public function initiate(SsoConfiguration $ssoConfiguration): RedirectInstruction
    {
        $endpoints = $this->oidcDiscoveryClient->fetch($this->stringConfig($ssoConfiguration, 'discovery_url'));

        $params = [
            'response_type' => 'code',
            'client_id' => $this->stringConfig($ssoConfiguration, 'client_id'),
            'redirect_uri' => $this->stringConfig($ssoConfiguration, 'redirect_uri'),
            'scope' => $this->stringConfigOrDefault($ssoConfiguration, 'scopes', 'openid email profile'),
            'state' => bin2hex(random_bytes(self::STATE_BYTES)),
        ];

        return new RedirectInstruction($endpoints['authorization_endpoint'].'?'.http_build_query($params));
    }

    public function complete(SsoConfiguration $ssoConfiguration, array $callbackPayload): SsoIdentity
    {
        $code = $callbackPayload['code'] ?? null;

        if (! is_string($code) || $code === '') {
            throw new SsoLoginRejectedException('missing_code');
        }

        $endpoints = $this->oidcDiscoveryClient->fetch($this->stringConfig($ssoConfiguration, 'discovery_url'));
        $tokens = $this->exchangeCode($ssoConfiguration, $endpoints['token_endpoint'], $code);
        $idToken = $tokens['id_token'] ?? null;

        if (! is_string($idToken)) {
            throw new SsoLoginRejectedException('missing_id_token');
        }

        $claims = $this->jwtPayloadDecoder->decode($idToken);
        $this->verifyAudience($ssoConfiguration, $claims);
        $this->verifyIssuer($endpoints, $claims);
        $this->verifyExpiration($claims);

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

        if (($endpoints['authorization_endpoint'] ?? '') === '') {
            $warnings[] = 'Missing authorization_endpoint in discovery document.';
        }

        if (($endpoints['token_endpoint'] ?? '') === '') {
            $warnings[] = 'Missing token_endpoint in discovery document.';
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

    /** @param array<string, scalar|null> $claims */
    private function verifyAudience(SsoConfiguration $ssoConfiguration, array $claims): void
    {
        $audience = isset($claims['aud']) && is_string($claims['aud']) ? $claims['aud'] : null;

        if ($audience !== $this->stringConfig($ssoConfiguration, 'client_id')) {
            throw new SsoLoginRejectedException('aud_mismatch');
        }
    }

    /**
     * @param  array<string, string>  $endpoints
     * @param  array<string, scalar|null>  $claims
     */
    private function verifyIssuer(array $endpoints, array $claims): void
    {
        $expected = $endpoints['issuer'] ?? null;

        if ($expected === null || $expected === '') {
            return;
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
