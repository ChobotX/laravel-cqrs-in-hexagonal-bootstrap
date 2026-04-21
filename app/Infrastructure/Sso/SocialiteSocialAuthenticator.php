<?php

declare(strict_types=1);

namespace App\Infrastructure\Sso;

use App\Domain\Sso\Contract\Entity\SsoConfiguration;
use App\Domain\Sso\Contract\Enum\ProviderType;
use App\Domain\Sso\Contract\Exception\SsoLoginRejectedException;
use App\Domain\Sso\Contract\Service\SsoAuthenticator;
use App\Domain\Sso\Contract\ValueObject\RedirectInstruction;
use App\Domain\Sso\Contract\ValueObject\SsoConnectionTestResult;
use App\Domain\Sso\Contract\ValueObject\SsoIdentity;
use App\Infrastructure\Sso\Exception\SsoConfigurationInvalidException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;

use function bin2hex;
use function http_build_query;
use function is_string;
use function random_bytes;

/**
 * OAuth2 adapter for Google, Microsoft, and GitHub.
 *
 * Secrets live in the tenant-encrypted `config` column and never leave the handler
 * request scope. Per-provider endpoints come from `SocialProviderCatalog`.
 */
final readonly class SocialiteSocialAuthenticator implements SsoAuthenticator
{
    private const int STATE_BYTES = 16;

    private const string USER_AGENT = 'laravel-cqrs-hexagonal-bootstrap';

    public function __construct(
        private HttpFactory $httpFactory,
        private SocialProviderCatalog $socialProviderCatalog,
        private GithubEmailFetcher $githubEmailFetcher,
    ) {}

    public function initiate(SsoConfiguration $ssoConfiguration): RedirectInstruction
    {
        $endpoints = $this->socialProviderCatalog->endpointsFor($ssoConfiguration->providerType->value);
        $state = bin2hex(random_bytes(self::STATE_BYTES));

        $params = [
            'response_type' => 'code',
            'client_id' => $this->stringConfig($ssoConfiguration, 'client_id'),
            'redirect_uri' => $this->stringConfig($ssoConfiguration, 'redirect_uri'),
            'scope' => $endpoints['scope'],
            'state' => $state,
        ];

        return new RedirectInstruction(
            url: $endpoints['authorize'].'?'.http_build_query($params),
            stateToStore: $state,
        );
    }

    public function complete(SsoConfiguration $ssoConfiguration, array $callbackPayload, ?string $expectedNonce = null): SsoIdentity
    {
        $code = $callbackPayload['code'] ?? null;

        if (! is_string($code) || $code === '') {
            throw new SsoLoginRejectedException('missing_code');
        }

        $endpoints = $this->socialProviderCatalog->endpointsFor($ssoConfiguration->providerType->value);
        $accessToken = $this->exchangeCode($ssoConfiguration, $endpoints['token'], $code);
        $info = $this->fetchUserInfo($endpoints['userinfo'], $accessToken);

        return $this->extractIdentity($ssoConfiguration->providerType, $info, $accessToken);
    }

    public function probe(SsoConfiguration $ssoConfiguration): SsoConnectionTestResult
    {
        try {
            $this->socialProviderCatalog->endpointsFor($ssoConfiguration->providerType->value);
        } catch (SsoConfigurationInvalidException $ssoConfigurationInvalidException) {
            // @silent: probe surfaces unsupported-provider errors as a UI-visible failure result.
            return new SsoConnectionTestResult(false, $ssoConfigurationInvalidException->getMessage());
        }

        if ($this->stringConfig($ssoConfiguration, 'client_id') === '') {
            return new SsoConnectionTestResult(false, 'client_id is missing.');
        }

        if ($this->stringConfig($ssoConfiguration, 'redirect_uri') === '') {
            return new SsoConnectionTestResult(false, 'redirect_uri is missing.');
        }

        return new SsoConnectionTestResult(true, 'Social provider configuration looks complete.');
    }

    private function exchangeCode(SsoConfiguration $ssoConfiguration, string $tokenUrl, string $code): string
    {
        try {
            $response = $this->httpFactory
                ->asForm()
                ->withHeaders(['Accept' => 'application/json'])
                ->post($tokenUrl, [
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
        $accessToken = $tokens['access_token'] ?? null;

        if (! is_string($accessToken) || $accessToken === '') {
            throw new SsoLoginRejectedException('missing_access_token');
        }

        return $accessToken;
    }

    /** @return array<string, scalar|null> */
    private function fetchUserInfo(string $userinfoUrl, string $accessToken): array
    {
        try {
            $response = $this->httpFactory
                ->withToken($accessToken)
                ->withHeaders(['Accept' => 'application/json', 'User-Agent' => self::USER_AGENT])
                ->get($userinfoUrl);
        } catch (ConnectionException $connectionException) {
            throw new SsoLoginRejectedException('userinfo_endpoint_unreachable: '.$connectionException->getMessage());
        }

        if (! $response->successful()) {
            throw new SsoLoginRejectedException('userinfo_fetch_failed');
        }

        /** @var array<string, scalar|null> $info */
        $info = (array) $response->json();

        return $info;
    }

    /** @param array<string, scalar|null> $info */
    private function extractIdentity(ProviderType $providerType, array $info, string $accessToken): SsoIdentity
    {
        return match ($providerType) {
            ProviderType::Github => $this->githubIdentity($info, $accessToken),
            default => $this->defaultIdentity($info),
        };
    }

    /** @param array<string, scalar|null> $info */
    private function defaultIdentity(array $info): SsoIdentity
    {
        $subject = isset($info['sub']) && is_string($info['sub']) ? $info['sub'] : null;
        $email = isset($info['email']) && is_string($info['email']) ? $info['email'] : null;

        if ($subject === null || $email === null) {
            throw new SsoLoginRejectedException('missing_required_claims');
        }

        $name = isset($info['name']) && is_string($info['name']) ? $info['name'] : null;

        return new SsoIdentity(subject: $subject, email: $email, name: $name);
    }

    /** @param array<string, scalar|null> $info */
    private function githubIdentity(array $info, string $accessToken): SsoIdentity
    {
        $subject = isset($info['id']) ? (string) $info['id'] : null;
        $name = isset($info['name']) && is_string($info['name']) ? $info['name'] : null;
        $email = isset($info['email']) && is_string($info['email']) ? $info['email'] : null;

        if ($email === null) {
            $email = $this->githubEmailFetcher->fetch($accessToken);
        }

        if ($subject === null || $email === null) {
            throw new SsoLoginRejectedException('missing_required_claims');
        }

        return new SsoIdentity(subject: $subject, email: $email, name: $name);
    }

    private function stringConfig(SsoConfiguration $ssoConfiguration, string $key): string
    {
        $value = $ssoConfiguration->config[$key] ?? null;

        return is_string($value) ? $value : '';
    }
}
